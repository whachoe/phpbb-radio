<?php
namespace Radio;

include_once __DIR__.'/../../config.php';

class Track {
  var $collection;

  function __construct($collection, $forums_to_ignore=array()) {
    // Mongo DB
    $this->collection = $collection;
    $this->forums_to_ignore = $forums_to_ignore;
  }

  /*
   *  @Deprecated
   */
  function getTrackAtPos($pos=0) {
    $track = $this->collection->find(array(
        'available' => true,
        'forum_id' => array('$nin'=> $this->forums_to_ignore))
      )->skip($pos)->getNext();

    return $this->prepareForSending($track);
  }

  public function getRandomTrack($forum_id=null) {
    $query = array('available' => true);

    if ($forum_id && !in_array($forum_id, $this->forums_to_ignore)) {
      $query['forum_id'] = $forum_id;
    } else {
      $query['forum_id'] = array('$nin'=> array('$nin'=> $this->forums_to_ignore));
    }

    $track = $this->collection->find($query)
            ->limit(-1)
            ->skip(mt_rand(0, $this->collection->count($query)))
            ->getNext();

    /* Alternative solution for random, with the random-column (but less even distribution)
     * The crawler still populates the random-field so this can be uncommented, but i prefer the solution above
     */
    /*
    $rand = mt_rand();
    $query['random'] = array('$gte' => $rand);
    $track = $this->collection->findOne($query);
    if ($track == null) {
      $query['random'] = array('$lte' => $rand);
      $track = $this->collection->findOne($query);
    }
    */

    try {
     $track = $this->prepareForSending($track);
    } catch (\Exception $e) {
      $track = $this->getRandomTrack($forum_id);
      return $track;
    }

    return $track;
  }

  public function getTrack($id) {
    $track = $this->collection->findOne(array(
        '_id' => new \MongoId($id),
        'available' => true,
        'forum_id' => array('$nin'=> $this->forums_to_ignore))
      );

    if (!$track) {
      throw new \Exception ("Track not available");
    }

    return $this->prepareForSending($track);
  }

  /*
   *  This function does a few things that need to be done before we send a track to the javascript-player
   */
  private function prepareForSending($track) {
    if (!isset($track['_id']))
      throw new \Exception ("Track not available");

    // Get Soundcloud Stream Url: In case the crawler didn't get it yet
    if (in_array($track['type'], array('soundcloud', 'soundcloud_embed')) && !isset($track['soundcloud_data'])) {
       $soundcloud_data = $this->getSoundcloudInfo($track);
       if (!$soundcloud_data)
         throw new \Exception("Soundcloud: Not a track");

       $track['soundcloud_data'] = $soundcloud_data;
    }

    // Updating play-count
    if (!isset($track['played']))
      $track['played'] = 1;
    else
      $track['played']++;

    $this->collection->save($track);


    // These are the only things the views need to know
    $toSend = array(
      'id'               => $track['_id']->__toString(),
      '_id'              => $track['_id'],
      'url'              => $track['url'],
      'type'             => $track['type'],
      'stream_url'       => $track['url'],
      'poster_name'      => $track['poster_name'],
      'forum_id'         => $track['forum_id'],
      'forum_name'       => $track['forum_name'],
      'post_id'          => $track['post_id'],
      'post_time'        => date("d M Y, h:i", $track['post_time']),
      'post_url'         => FORUM_URL.'/viewtopic.php?f='.$track['forum_id'].'&p='.$track['post_id'],
      'forum_url'        => FORUM_URL.'/viewforum.php?f='.$track['forum_id'],
      'available'        => $track['available'],
    );

    switch ($track['type']) {
      case 'soundcloud_embed':
      case 'soundcloud' :
        $toSend['type_img']   = 'img/type_soundcloud.png';
      	$toSend['stream_url'] = $track['soundcloud_data']->stream_url;
      	$toSend['streamable'] = $track['soundcloud_data']->streamable;
        $toSend['songtitle']  = $track['soundcloud_data']->title;
        $toSend['artist']     = $track['soundcloud_data']->user->username;
        $toSend['soundcloud_api_key'] = SOUNDCLOUD_API_KEY;
      	break;

      case 'mp3' :
      default:
        $toSend['type_img']   = 'img/type_mp3.png';
        $toSend['songtitle']  = str_replace(array('.mp3', '.ogg'), '', substr($toSend['url'], strrpos($toSend['url'], '/')+1));
        $toSend['artist']     = 'Unknown';
        $toSend['stream_url'] = $toSend['url'];
        break;
    }
    return $toSend;
  }

  private function getSoundcloudInfo($track) {
    $track_url = $track['url'];
    $data = file_get_contents("http://api.soundcloud.com/resolve.json?client_id=".SOUNDCLOUD_API_KEY."&url=".urlencode($track_url));
    if ($data) {
        $d = json_decode($data);
        if (property_exists($d, 'kind'))
          if ($d->kind != 'track')
            return null;

        if (property_exists($d, 'streamable'))
          if ($d->streamable)
            return $d;
    }

    return null;
  }
}

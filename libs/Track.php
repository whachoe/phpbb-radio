<?php
class Track {
  var $collection;
  
  function __construct($collection) {
    // Mongo DB
    $this->collection = $collection;
  }

  /*
   *  @Deprecated
   */
  function getTrackAtPos($pos=0) {
    global $forums_to_ignore; 
    
    $track = $this->collection->find(array(
        'available' => true, 
        'forum_id' => array('$nin'=> $forums_to_ignore))
      )->skip($pos)->getNext();
    
    return $this->prepareForSending($track);
  }
  
  public function getRandomTrack($forum_id) {
    global $forums_to_ignore; 
    
    $query = array('available' => true);
    
    if ($forum_id && !in_array($forum_id, $forums_to_ignore)) {
      $query['forum_id'] = $forum_id;
    } else {
      $query['forum_id'] = array('$nin'=> array('$nin'=> $forums_to_ignore));
    }
    
    $track = $this->collection->find($query)
            ->limit(-1)
            ->skip(mt_rand(0, $this->collection->count($query)))
            ->getNext();
    
    /* Alternative solution for random, with the random-column (but less even distribution)
    $rand = mt_rand();
    $query['random'] = array('$gte' => $rand);
    $track = $this->collection->findOne($query);
    if ($track == null) {
      $query['random'] = array('$lte' => $rand); 
      $track = $this->collection->findOne($query);
    }
    */
    return $this->prepareForSending($track);
  }
  
  public function getTrack($id) {
    global $forums_to_ignore; 
    
    $track = $this->collection->findOne(array(
        '_id' => new MongoId($id),
        'available' => true, 
        'forum_id' => array('$nin'=> $forums_to_ignore))
      );
    
    if (!$track) {
      throw new Exception ("Track not available");
    }
    
    return $this->prepareForSending($track);
  }
  
  /*
   *  This function does a few things that need to be done before we send a track to the javascript-player
   */
  private function prepareForSending($track) {
    if (!isset($track['_id']))
      throw new Exception ("Track not available");
    
    // Updating play-count
    if (!isset($track['played']))
      $track['played'] = 1;
    else 
      $track['played']++;
    
    $this->collection->save($track);
    
    // These are things only the views need to know
    $track['id'] = $track['_id']->__toString();
    $track['post_time'] = date("d M Y, h:i", $track['post_time']);
    switch ($track['type']) {
      case 'soundcloud_embed':
      case 'soundcloud' : $track['type_img'] = '/img/type_soundcloud.png';
      break;
    
      case 'mp3' : 
      default:  
        $track['type_img'] = '/img/type_mp3.png';
        break;
    }
    return $track;
  }
}
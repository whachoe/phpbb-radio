<?php
class Track {
  var $m;
  var $mongodb;
  var $collection;
  
  function __construct() {
    // Mongo DB
    $this->m = new Mongo();
    $this->mongodb = $this->m->breakzradio;
    $this->collection = $this->mongodb->tracks;
  }


  function getTrackAtPos($pos=0) {
    $track = $this->collection->find(array(
        'available' => true, 
        'forum_id' => array('$nin'=> array("13","16","19","11","12")))
      )->skip($pos)->getNext();
    
    return $this->prepareForSending($track);
  }
  
  function getRandomTrack($forum_id) {
    $rand = mt_rand();
    
    $query = array(
                  'available' => true,
                  'random' => array('$gte' => $rand));
    if ($forum_id) {
      $query['forum_id'] = $forum_id;
    } else {
      $query['forum_id'] = array('$nin'=> array("13","16","19","11","12"));
    }
    
    $result = $this->collection->findOne($query);
    if ($result == null) {
      $result = $this->collection->findOne($query);
    }
    
    return $this->prepareForSending($result);
  }
  
  function getTrack($id) {
    $track = $this->collection->findOne(array(
        '_id' => new MongoId($id),
        'available' => true, 
        'forum_id' => array('$nin'=> array("13","16","19","11","12")))
      );
    
    if (!$track) {
      throw new Exception ("Track not available");
    }
    
    return $this->prepareForSending($track);
  }
  
  // This function does a few things that need to be done before we send a track to the javascript-player
  function prepareForSending($track) {
    if (!isset($track['_id']))
      throw new Exception ("Track not available");
    
    // Updating play-count
    if (!isset($track['played']))
      $track['played'] = 1;
    else 
      $track['played']++;
    
    $this->collection->save($track);
    
    // These are things only the views need to know
    $track['id'] = $track['_id']->{'$id'};
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
<?php
require_once '../../libs/Track.php';

class apiController {
	/**
	 * Play the next track
	 * 
	 * @url GET /next
	 */
	public function next($data = null) {
    //var_dump($data); die;
    
		$trackObject = new Track();
    
    // Parameter processing
    $pos = isset($_GET['pos']) && is_numeric($_GET['pos']) ? $_GET['pos'] : 0;
    $skipped = $_GET['skipped'];
    $selected_forum = $_GET['forum_id'];
    $previous_track = $_GET['previous_track_id'];
    
    // TODO: register the skip in the previous-track record
    
    //$track = $trackObject->getTrackAtPos($pos);
    try {
      $track = $trackObject->getRandomTrack($selected_forum);
      return $track; 
    } catch (Exception $e) {
      // TODO: Log something maybe?
    }
	}
  
  /**
   * Play a specific track
   * 
   * @url GET /play
   */
  public function play() {
    $id = trim($_GET['id']);
   
    if ($id != null) {
      try {
        $trackObject = new Track();  
        $track = $trackObject->getTrack($id);
        return $track;
      } catch (Exception $e) {
        // TODO: Log something maybe?
        return array("error" => "Track not found");
      }
    }
  }

	/**
	 *
	 * @url POST /message
	 * @url PUT  /message
	 */
  /*
	public function newMessage($data) {
		// Do some validation here
		file_put_contents(self::message_file_path, json_encode($data));

		return true;
	}
   * 
   */
}

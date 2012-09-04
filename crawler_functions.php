<?php
  // Check if a url is valid and accessible
  function url_valid($url) {
    // Quick check to see if url exists
    if ($f = @fopen($url,"r"))  {
      // If so: Let's check if we don't get redirected
      $headers = get_headers($url);
      if (strpos($headers[0], '200') !== FALSE) {
        // Finally: Check if content-type matches
        if (isset($headers[8]) && strpos($headers[8], 'audio') !== false)	
          return true;
      }
      fclose($f);
    }
    return false;
  }

  function soundcloud_valid($url) {
    // Quick check to see if url exists
    if ($f = @fopen($url,"r"))  {
      // If so: Let's check if we don't get redirected
      $headers = get_headers($url);
      if (strpos($headers[0], '200') !== FALSE) {
        return true;
      }
      fclose($f);
    }
    return false;
  }
  
  // Get data from the Forum table
  function fetch_forum_info($forum_id, $db) {
    $sql = "SELECT * FROM phpbb_forums WHERE forum_id=$forum_id LIMIT 1";
    return $db->query($sql)->fetch();
  }

  // Get data from the Topic/Thread table
  function fetch_topic_info($topic_id, $db) {
    $sql = "SELECT * FROM phpbb_topics WHERE topic_id=$topic_id";
    return $db->query($sql)->fetch();
  }
  
  function fetch_user_info($user_id, $db) {
    $sql = "SELECT * FROM phpbb_users WHERE user_id=$user_id";
    return $db->query($sql)->fetch();
  }
  
  // Makes a record out of a post-row
  function make_record($row, $db) {
    $record = array();
		$record['post_id']    = (int) $row['post_id'];
		$record['post_time']  = $row['post_time'];
		$record['topic_id']   = $row['topic_id'];
		$record['forum_id']   = $row['forum_id'];
		$record['poster_id']  = $row['poster_id'];
		$record['post_title'] = $row['post_subject'];
    $record['available']  = false; // initialize to false. will be overwritten by the crawler
    $record['random']     = mt_rand();

    // Fetch all other relevant info
    $forum_info = fetch_forum_info($record['forum_id'], $db);
    $topic_info = fetch_topic_info($record['topic_id'], $db);
    $user_info  = fetch_user_info($record['poster_id'], $db);
    
    $record['forum_name'] = $forum_info['forum_name'];
    $record['topic_title']= $topic_info['topic_title'];
    $record['poster_name']= $user_info['username'];
    
    return $record;
  }
  
  function add_record($record, $collection) {
    // Check if url is already in db
    $maybe_exists = $collection->findOne(array("url" => $record['url']));
    // If so: return
    if (is_array($maybe_exists)) {
      echo "{$record['url']}: Already exists\n";
      return;
    }
    
    $collection->insert($record);
    
    echo json_encode($record);
		echo "\n";
  }
  
  function url_exists_in_collection($url, $collection) {
    // Check if url is already in db
    $maybe_exists = $collection->findOne(array("url" => $url));
    // If so: return
    if (is_array($maybe_exists)) {
      return true;
    }

    return false;
  }
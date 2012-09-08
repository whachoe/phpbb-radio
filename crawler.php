<?php
include_once 'config.php';
require_once 'crawler_functions.php';

$db = new PDO(MYSQL_DSN, MYSQL_USER, MYSQL_PW, $options);

// Mongo DB
$m = new Mongo();
$mongodb = $m->selectDB(MONGO_DB);
$collection = $mongodb->selectCollection(MONGO_COLLECTION);

// Indexes
$collection->ensureIndex(array("url" => 1), array("unique" => 1, "dropDups" => 1));
$collection->ensureIndex(array("available" => 1));
$collection->ensureIndex(array("available" => 1, "forum_id" => 1));
$collection->ensureIndex(array('available' => 1, 'random' => 1));

// Getting the highest post_id so we can update incrementally
$max_post_record = $collection->find(array(), array('post_id' => true))->sort(array('post_id' => -1))->limit(1)->getNext();
if ($max_post_record)
  $max_post_id = $max_post_record['post_id'];
else 
  $max_post_id = 0;

// Uncomment next line if you want to reindex the complete db again
$max_post_id = 0;

/*****  Indexing ****/

// MP3 and OGG
/*
$regex = 'href="(.*\.(mp3|ogg))"';
$preg  = '/href="(.*?\.(mp3|ogg))"/';

$result = $db->query("SELECT * from phpbb_posts WHERE post_id > $max_post_id AND post_text REGEXP '$regex'");
foreach ($result as $row ) {
  // If posted in one of the $forums_to_ignore, just skip to the next entry
  if (in_array($row['forum_id'], $forums_to_ignore))
    continue;
      
	$allmatches = array();
	preg_match_all($preg, $row['post_text'], $allmatches, PREG_PATTERN_ORDER);
	if (isset($allmatches[1])) {
    foreach ($allmatches[1] as $url) {
      if (url_exists_in_collection($url, $collection))
        continue;
      
      // Initialize record 
      $record = make_record($row, $db);
      $record['url']        = $url;
      $record['type']       = 'mp3';

      // Quick check to see if url exists
      if (url_valid($record['url'])) {
        $record['available'] = true;
      }

      add_record($record, $collection);
    } 
  }
}
*/

// Soundcloud regular links
$regex= 'href="(http:\/\/soundcloud\.com\/.*\/.*)">';
$preg  = '/href="(http:\/\/soundcloud\.com\/.*\/.*)">/';

$result = $db->query("SELECT * from phpbb_posts WHERE post_id > $max_post_id AND post_text REGEXP '$regex'");
foreach ($result as $row ) {
  // If posted in one of the $forums_to_ignore, just skip to the next entry
  if (in_array($row['forum_id'], $forums_to_ignore))
    continue;
      
  $allmatches = array();
  preg_match_all($preg, $row['post_text'], $allmatches, PREG_PATTERN_ORDER);
	if (isset($allmatches[1])) {
    foreach ($allmatches[1] as $url) {
      // if (url_exists_in_collection($url, $collection))
      //  continue;
      
      // Initialize record 
      $record = make_record($row, $db);
      $record['url']       = $url;
      $record['type'] = 'soundcloud';
      
      // Parse data from soundcloud API
      $soundcloud_data = getSoundcloudData($url);
      if ($soundcloud_data) {
        if ($soundcloud_data->kind == 'track') {
          if ($soundcloud_data->streamable) {
            $record['stream_url'] = $soundcloud_data->stream_url;
            $record['available'] = true;
            $record['soundcloud_data'] = $soundcloud_data;
            add_record($record, $collection);
            continue;
          } else {
            $record['available'] = false;
          }
        } elseif ($soundcloud_data->kind == 'playlist') {
          foreach ($soundcloud_data->tracks as $track) {
            $r = make_record($row, $db);
            $r['url'] = $soundcloud_data->permalink_url;
            $r['type'] = 'soundcloud';
            $r['stream_url'] = $soundcloud_data->stream_url;
            $r['soundcloud_data'] = $soundcloud_data;
            add_record($r, $collection);
            continue;
          }
        } else {
          continue;
        } 
      }
    }
  }
}

die;

// Soundcloud embedded links
/*
 * [soundcloud:9wavub5k]http&#58;//soundcloud&#46;com/renelavice/1xtra-dnb-with-bailey-13-04-11[/soundcloud:9wavub5k]
 */
$regex = '\[soundcloud:(.*)\](.*)\[\/soundcloud:.*\]';
$preg = '/\[soundcloud:(.*?)\](.*?)\[\/soundcloud:.*?\]/';
$result = $db->query("SELECT * from phpbb_posts WHERE post_id > $max_post_id AND post_text REGEXP '$regex'");
foreach ($result as $row ) {
  // If posted in one of the $forums_to_ignore, just skip to the next entry
  if (in_array($row['forum_id'], $forums_to_ignore))
    continue;

	$allmatches = array();
	preg_match_all($preg, $row['post_text'], $allmatches, PREG_PATTERN_ORDER);
  
	if (isset($allmatches[2])) {
    foreach ($allmatches[2] as $match) {
      $url = html_entity_decode($match);
      if (url_exists_in_collection($url, $collection))
        continue;
      
      // Initialize record 
      $record = make_record($row, $db);
      $record['url']  = $url;
      $record['type'] = 'soundcloud_embed';

      // Quick check to see if url exists
      if (soundcloud_valid($record['url'])) {
        $record['available'] = true;
      }

      add_record($record, $collection);
    }
  }
}

<?php
include_once '../config.php';
require_once '../vendor/autoload.php';
$loader = require_once 'bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Radio\Track;

/**
 * Returns a SELECT-box with all the valid forums
 * 
 * @global type $mongodb
 * @global type $collection
 * @global type $forums_to_ignore
 * @return string
 */
function filterbox_forum() {
  global $mongodb, $collection,$forums_to_ignore;

  $distinct_forums = $mongodb->command(
          array("distinct" => "tracks", 
                "key" => "forum_id",
                // We're filtering out a bunch of forum-id's which are private
                // Normally we should not have any such tracks since they are skipped in the crawler,
                // but those forums might have been set 'private' after the indexing
                "query" => array('forum_id' => array('$nin'=> $forums_to_ignore), 'available' => true)
              ));
  
  $out = '<select id="filter_forum">';
  $out .= '<option value="0">Choose Forum</option>' . "\n";
  foreach ($distinct_forums['values'] as $forum_id) {
    $record = $collection->findOne(array('forum_id' => $forum_id), array('forum_name'));
    $out .= "<option value=\"$forum_id\">{$record['forum_name']}</option>\n";
  }
  $out .= '</select>' . "\n";

  return $out;
}

// Mongo DB
$m = new Mongo();
$mongodb = $m->selectDB(MONGO_DB);
$collection = $mongodb->selectCollection(MONGO_COLLECTION);

// Setting up the controller
$app = new Silex\Application();
$app['debug'] = true;
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => '../logs/radio.log',
));


// API Controller
$api = $app['controllers_factory'];

/**
  * Play a specific track
  */ 
$api->get('/play', function() use ($app, $collection) {
  $id = trim($_GET['id']);
   
    if ($id != null) {
      try {
        $trackObject = new Track($collection);  
        $track = $trackObject->getTrack($id);
        
        return $app->json($track);
      } catch (Exception $e) {
        $app['monolog']->addError("API - play - Track not found: ".$id);
        return $app->json("Track not found", 404);
      }
    }
    
    return $app->json("Track not found", 404);
});

/**
  * Play the next track
	*/
$api->get('/next', function() use ($app, $collection) {
  // Parameter processing
  $pos = isset($_GET['pos']) && is_numeric($_GET['pos']) ? $_GET['pos'] : 0;
  $skipped = $_GET['skipped'];
  $selected_forum = $_GET['forum_id'];
  $previous_track = $_GET['previous_track_id'];

  // TODO: register the skip in the previous-track record

  try {
    $trackObject = new Track($collection);
    $track = $trackObject->getRandomTrack($selected_forum);
    
    return $app->json($track);
  } catch (Exception $e) {
    $app['monolog']->addError("API - next - No track not found");
    return $app->json("Track not found", 404);
  }
  
  return $app->json("Track not found", 404);
});

// Make sure that only the previous 2 actions are defined
$api->get('/', function(){ $app->abort(404, 'This action is not defined'); });

// Route to API-controller
$app->mount('/api', $api);



// Route to mainpage
$app->get('/', function(Request $request) use ($collection) {
  $totalTrackCount = $collection->count();
  $totalActiveCount = $collection->count(array("available" => true));
  $totalInactiveCount = $collection->count(array("available" => false));
  
  // Include the view
  include_once '../views/index.php';
  
})->bind('homepage');

return $app;

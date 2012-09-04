<?php
//include_once 'config.php';
include_once 'crawler_functions.php';

// Mongo DB
$m = new Mongo();
$mongodb = $m->breakzradio;
$collection = $mongodb->tracks;
$collection->ensureIndex(array("url" => 1), array("unique" => 1, "dropDups" => 1));
$collection->ensureIndex(array("available" => 1));

$cursor = $collection->find(array('available' => true));
foreach ($cursor as $record) {
  if ($record['type'] == 'mp3')
    $newrecord['available'] = url_valid($record['url']);
  if ($record['type'] == 'soundcloud' || $record['type'] == 'soundcloud_embed')
    $newrecord['available'] = soundcloud_valid ($record['url']);
  
  $collection->update(array("_id" => $record['_id']), array('$set' => $newrecord));
}

<?php
/**
* Utility script: When we need to do some processing of mongo-db, i'll use this one and just adapt the code a bit
*/
include_once 'config.php';
include_once 'crawler_functions.php';



// Mysql
$db = new PDO(MYSQL_DSN, MYSQL_USER, MYSQL_PW, $options);

// Mongo DB
$m = new Mongo();
$mongodb = $m->selectDB(MONGO_DB);
$collection = $mongodb->selectCollection(MONGO_COLLECTION);
$collection->ensureIndex(array("url" => 1), array("unique" => 1, "dropDups" => 1));
$collection->ensureIndex(array("available" => 1));

$cursor = $collection->find();
foreach ($cursor as $record) {
  $newrecord['url'] = str_replace('\/', '/', $record['url']);
  $newrecord['available'] = true;
  
  $collection->update(array("_id" => $record['_id']), array('$set' => $newrecord));
}

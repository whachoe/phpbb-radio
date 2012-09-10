<?php 
$loader = require_once __DIR__.'/../app/bootstrap.php';
$loader->add('Radio\\Tests', __DIR__);

function getCollection() {
	$conn = new Mongo();
	$db =  $conn->radiotest;
  return $db->tracks;
}

function loadFixtures() {
  $fixturepath = __DIR__."/mongo_fixtures.json";
 
  $fixtures = json_decode(file_get_contents($fixturepath));
  $collection = getCollection();
  foreach ($fixtures as $fixt) {
    $collection->insert($fixt);
  }
}
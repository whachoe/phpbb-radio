<?php
use Radio\Track;

class TrackTest extends PHPUnit_Framework_TestCase {
  public function testGetRandomTrack() {
    $collection = getCollection();
    $track = new Track($collection);
    $record = $track->getRandomTrack();
    
    $this->assertNotEmpty($record);
    $this->assertArrayHasKey('_id', $record);
    $this->assertEquals($record['available'], true);
    $this->assertEquals($record['_id']->__toString(), $record['id']);
    $this->assertStringStartsWith("http://", $record['url']);
    
    return $record;
  }
  
  /**
   *  @depends testGetRandomTrack
   */
  public function testGetTrack($randomTrack) {
    $collection = getCollection();
    $track = new Track($collection);

    $record = $track->getTrack($randomTrack['id']);
    $this->assertNotEmpty($record);
    $this->assertArrayHasKey('_id', $record);
    $this->assertEquals($record['available'], true);
    $this->assertStringStartsWith("http://", $record['url']);
    $this->assertEquals($record['id'], $randomTrack['_id']->__toString());
  }
  
  
  
  public static function setUpBeforeClass() {
    loadFixtures();
  }
  
  public static function tearDownAfterClass() {
    $coll = getCollection();
    $coll->remove(array());
  }
}

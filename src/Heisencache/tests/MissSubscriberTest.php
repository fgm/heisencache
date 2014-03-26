<?php
/**
 * @file
 * Unit tests for the MissSubscriber class.
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache\tests;


use OSInet\Heisencache\MissSubscriber;

class MissSubscriberTest extends \PHPUnit_Framework_TestCase {

  const CHANNEL = "some channel";

  protected $emitter;

  public function setUp() {
    $this->emitter = $this->getMock('OSInet\Heisencache\EventEmitter');
  }

  public function testGetHit() {
    $sub = new MissSubscriber($this->emitter);
    $actual = $sub->afterGet(self::CHANNEL, 'k', 'v');
    $this->assertInternalType('array', $actual);
    $this->assertEmpty($actual);
  }

  public function testGetMiss() {
    $key = 'some_key';

    $sub = new MissSubscriber($this->emitter);
    $actual = $sub->afterGet(self::CHANNEL, $key, FALSE);

    $this->assertInternalType('array', $actual);
    $this->assertNotEmpty($actual);
    $this->assertArrayHasKey('misses', $actual);
    $this->assertNotEmpty($actual['misses']);
    $this->assertTrue(in_array($key, $actual['misses']));
  }

  public function testGetMultipleWithMisses() {
    $initial_cids = array('k1', 'k2', 'k3');
    $missed_cids = array('k1', 'k3');

    $sub = new MissSubscriber($this->emitter);
    $sub->beforeGetMultiple(self::CHANNEL, $initial_cids);
    $actual = $sub->afterGetMultiple(self::CHANNEL, $missed_cids);

    $this->assertInternalType('array', $actual);
    $this->assertNotEmpty($actual);
    $this->assertArrayHasKey('full_miss', $actual);
    $this->assertFalse($actual['full_miss']);
    $this->assertArrayHasKey('misses', $actual);
    $this->assertNotEmpty($actual['misses']);
    foreach ($missed_cids as $cid) {
      $this->assertTrue(in_array($cid, $actual['misses']));
    }
  }

  public function testGetMultipleWithFullMiss() {
    $initial_cids = array('k1', 'k2', 'k3');
    $missed_cids = $initial_cids;

    $sub = new MissSubscriber($this->emitter);
    $sub->beforeGetMultiple(self::CHANNEL, $initial_cids);
    $actual = $sub->afterGetMultiple(self::CHANNEL, $missed_cids);

    $this->assertInternalType('array', $actual);
    $this->assertNotEmpty($actual);
    $this->assertArrayHasKey('full_miss', $actual);
    $this->assertTrue($actual['full_miss']);
    $this->assertArrayHasKey('misses', $actual);
    $this->assertNotEmpty($actual['misses']);
    foreach ($missed_cids as $cid) {
      $this->assertTrue(in_array($cid, $actual['misses']));
    }
  }

  public function testGetMultipleWithoutMisses() {
    $initial_cids = array('k1', 'k2', 'k3');
    $missed_cids = array();

    $sub = new MissSubscriber($this->emitter);
    $sub->beforeGetMultiple(self::CHANNEL, $initial_cids);
    $actual = $sub->afterGetMultiple(self::CHANNEL, $missed_cids);
    $this->assertInternalType('array', $actual);
    $this->assertEmpty($actual);

  }
}

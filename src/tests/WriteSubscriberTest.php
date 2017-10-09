<?php
/**
 * @file
 * Unit tests for the WriteSubscriber class.
 *
 * @author: bpresles
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache\tests;


use OSInet\Heisencache\WriteSubscriber;

class WriteSubscriberTest extends \PHPUnit_Framework_TestCase {

  const CHANNEL = "some channel";

  protected $emitter;

  public function setUp() {
    $this->emitter = $this->getMock('OSInet\Heisencache\EventEmitter');
  }

  public function testSet() {
    $sub = new WriteSubscriber($this->emitter);
    $value = 'v';
    $serialized_value = serialize($value);

    $actual = $sub->afterSet(self::CHANNEL, 'k', $value, 120);
    $this->assertInternalType('array', $actual);
    $this->assertNotEmpty($actual);
    $this->assertArrayHasKey('value_size', $actual);
    $this->assertEquals(strlen($serialized_value), $actual['value_size']);
  }

  public function testClear() {
    $sub = new WriteSubscriber($this->emitter);
    $wildcard = TRUE;
    $actual = $sub->afterClear(self::CHANNEL, 'k', $wildcard);

    $this->assertInternalType('array', $actual);
    $this->assertNotEmpty($actual);
    $this->assertArrayHasKey('wildcard', $actual);
    $this->assertEquals($wildcard, $actual['wildcard']);
  }

  public function testGetEmittedEvents() {
    $sub = new WriteSubscriber($this->emitter);
    $actual = $sub->getEmittedEvents();

    $this->assertInternalType('array', $actual);
    $this->assertNotEmpty($actual);
    $this->assertEquals(1, count($actual));
    $this->assertEquals('write', $actual[0]);
  }

}

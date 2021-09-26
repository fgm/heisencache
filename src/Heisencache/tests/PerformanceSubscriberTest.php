<?php
/**
 * @file
 * PerformanceSubscriberTest.php
 *
 * @copyright (c) 2014-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache\tests;

use OSInet\Heisencache\PerformanceSubscriber;
use PHPUnit\Framework\TestCase;

class PerformanceSubscriberTest extends TestCase {

  const CHANNEL = "some channel";

  protected $emitter;

  public function testGetTimerIdNoCids() {
    $channel = static::CHANNEL;
    $cids = array();
    $actual = PerformanceSubscriber::getTimerId($channel, $cids);
    $expected = serialize(array(static::CHANNEL));
    $this->assertEquals($expected, $actual);
  }

  public function testGetTimerIdCids() {
    $channel = static::CHANNEL;
    $cids = array('foo', 'bar');
    $actual = PerformanceSubscriber::getTimerId($channel, $cids);
    $expected = serialize(array(static::CHANNEL, 'foo', 'bar'));
    $this->assertEquals($expected, $actual);
  }
}

<?php
/**
 * @file
 * PerformanceSubscriberTest.php
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache\tests;

use OSInet\Heisencache\PerformanceSubscriber;

class PerformanceSubscriberTest extends \PHPUnit_Framework_TestCase {

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

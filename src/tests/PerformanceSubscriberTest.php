<?php

declare(strict_types=1);

namespace Drupal\heisencache\tests;

use Drupal\heisencache\EventSubscriber\PerformanceSubscriber;
use PHPUnit\Framework\TestCase;

/**
 * Test the PerformanceSubscriber.
 *
 * @copyright (c) 2014-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
class PerformanceSubscriberTest extends TestCase {

  const CHANNEL = "some channel";

  protected $emitter;

  public function testGetTimerIdNoCids() {
    $channel = static::CHANNEL;
    $cids = [];
    $actual = PerformanceSubscriber::getTimerId($channel, $cids);
    $expected = serialize([static::CHANNEL]);
    $this->assertEquals($expected, $actual);
  }

  public function testGetTimerIdCids() {
    $channel = static::CHANNEL;
    $cids = ['foo', 'bar'];
    $actual = PerformanceSubscriber::getTimerId($channel, $cids);
    $expected = serialize([static::CHANNEL, 'foo', 'bar']);
    $this->assertEquals($expected, $actual);
  }

}

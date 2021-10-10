<?php

declare(strict_types=1);

namespace Drupal\heisencache\tests;

use _PHPStan_76800bfb5\Evenement\EventEmitter;
use Drupal\heisencache\EventSubscriber\WriteSubscriber;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the WriteSubscriber class.
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
class WriteSubscriberTest extends TestCase {

  const CHANNEL = "some channel";

  protected ?EventEmitter $emitter = NULL;

  public function setUp(): void {
    $this->emitter = $this->getMockBuilder(EventEmitter::class)->getMock();
  }

  public function testSet() {
    $sub = new WriteSubscriber($this->emitter);
    $value = 'v';
    $serialized = serialize($value);

    $actual = $sub->afterSet(self::CHANNEL, 'k', $value, 120);
    $this->assertIsArray($actual);
    $this->assertNotEmpty($actual);
    $this->assertArrayHasKey('value_size', $actual);
    $this->assertEquals(strlen($serialized), $actual['value_size']);
  }

  public function testClear() {
    $sub = new WriteSubscriber($this->emitter);
    $wildcard = TRUE;
    $actual = $sub->afterClear(self::CHANNEL, 'k', $wildcard);

    $this->assertIsArray($actual);
    $this->assertNotEmpty($actual);
    $this->assertArrayHasKey('wildcard', $actual);
    $this->assertEquals($wildcard, $actual['wildcard']);
  }

  public function testGetEmittedEvents() {
    $sub = new WriteSubscriber($this->emitter);
    $actual = $sub->getEmittedEvents();

    $this->assertIsArray($actual);
    $this->assertNotEmpty($actual);
    $this->assertEquals(1, count($actual));
    $this->assertEquals('write', $actual[0]);
  }

}

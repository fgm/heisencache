<?php

declare(strict_types=1);

namespace Drupal\heisencache\tests;

use Drupal\heisencache\Cache\Cache;
use Drupal\heisencache\EventSubscriber\DebugSubscriber;
use Drupal\heisencache\EventEmitter;
use Drupal\heisencache\EventSubscriber\MissSubscriber;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the DebugSubscriber class.
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
class DebugSubscriberTest extends TestCase {

  /**
   * Fully qualified class name for DebugSubscriber.
   */
  const FQCN = 'Drupal\heisencache\DebugSubscriber';

  protected $events = NULL;

  public function setUp(): void {
    $this->events = array_merge(
      Cache::getEmittedEvents(),
      MissSubscriber::getEmittedEvents()
    );
  }

  public function testExplicitEventRegistration() {
    $event1 = 'event1';
    $event2 = 'event2';
    $events = [$event1, $event2];

    $sub = new DebugSubscriber($events);
    $actual = $sub->getSubscribedEvents();
    $this->assertEquals($actual, $events);
  }

  public function testImplicitEventRegistration() {
    $sub = new DebugSubscriber();
    $actual = $sub->getSubscribedEvents();
    $this->assertEquals($actual, $this->events);
  }

  public function testEventHandling() {
    $channel = "some_bin";

    /** @var \Drupal\heisencache\tests\MockEventSubscriberInterface $mock */
    $mock = $this->getMockBuilder(self::FQCN)
      ->setMethods(['getSubscribedEvents', 'show'])
      ->getMock();
    $mock->expects($this->exactly(count($this->events)))
      ->method('show');
    $mock->expects($this->once())
      ->method('getSubscribedEvents')
      ->will($this->returnValue($this->events));

    $emitter = new EventEmitter();
    try {
      $emitter->register($mock);
    } catch (\InvalidArgumentException $e) {
      $this->fail("Registering the DebugSubscriber on the Emitter failed: " . $e->getMessage());
    }

    $eventMap = [
      'onCacheConstruct' => ['bin'],
      'onShutdown' => ['bin'],

      'beforeGet' => ['k'],
      'afterGet' => ['k', 'v'],

      'beforeGetMultiple' => [['k1', 'k2']],
      'afterGetMultiple' => [['k1', 'k2']],

      'beforeSet' => ['k', 'v'],
      'afterSet' => ['k', 'v'],

      'beforeClear' => [],
      'afterClear' => [],

      'beforeIsEmpty' => [],
      'afterIsEmpty' => [],

      'miss' => [],
      'missMultiple' => [],
    ];

    foreach ($eventMap as $eventName => $eventArgs) {
      array_unshift($eventArgs, $channel);
      array_unshift($eventArgs, $eventName);
      $notified = call_user_func_array([$emitter, 'emit'], $eventArgs);
      $this->assertEquals(1, $notified,
        "Event $eventName caused one notification");
    }
  }

  public function testShow() {
    $arg = 'foo';
    $sub = new DebugSubscriber();
    $sub->show($arg);
    $this->expectOutputRegex('/' . __FUNCTION__ . "\($arg\)/");
  }

}

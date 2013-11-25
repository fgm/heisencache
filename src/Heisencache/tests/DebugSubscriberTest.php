<?php
/**
 * @file
 * Unit tests for the DebugSubscriber class.
 *
 * @author: marand
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache\tests;


use OSInet\Heisencache\Cache;
use OSInet\Heisencache\DebugSubscriber;
use OSInet\Heisencache\EventEmitter;

class DebugSubscriberTest extends \PHPUnit_Framework_TestCase {
  /**
   * Fully qualified class name for DebugSubcriber.
   */
  const FQCN = 'OSInet\Heisencache\DebugSubscriber';

  protected $events = NULL;

  public function setUp() {
    $this->events = Cache::getEvents();
  }

  public function testExplicitEventRegistration() {
    $event1 = 'event1';
    $event2 = 'event2';
    $events = array($event1, $event2);

    $sub = new DebugSubscriber($events);
    $actual = $sub->getEvents();
    $this->assertEquals($actual, $events);
  }

  public function testImplicitEventRegistration() {
    $sub = new DebugSubscriber();
    $actual = $sub->getEvents();
    $this->assertEquals($actual, $this->events);
  }

  public function testEventHandling() {
    $mock = $this->getMockBuilder(self::FQCN)
      ->setMethods(array('getEvents', 'show'))
      ->getMock();
    $mock->expects($this->exactly(count($this->events)))
      ->method('show');
    $mock->expects($this->once())
      ->method('getEvents')
      ->will($this->returnValue($this->events));

    $emitter = new EventEmitter();
    $emitter->register($mock);

    $eventMap = array(
      'onCacheConstruct' => array('bin'),

      'beforeGet' => array('k'),
      'afterGet' => array('k', 'v'),

      'beforeGetMultiple' => array(array('k1', 'k2')),
      'afterGetMultiple' =>array( array('k1', 'k2')),

      'beforeSet' => array('k', 'v'),
      'afterSet' => array('k', 'v'),

      'beforeClear' => array(),
      'afterClear' => array(),

      'beforeIsEmpty' => array(),
      'afterIsEmpty' => array(),
    );

    foreach ($eventMap as $eventName => $eventArgs) {
      array_unshift($eventArgs, $eventName);
      $notified = call_user_func_array(array($emitter, 'emit'), $eventArgs);
      $this->assertEquals(1, $notified, "Event $eventName caused one notification");
    }
  }

  public function testShow() {
    $arg = 'foo';
    $sub = new DebugSubscriber();
    $sub->show($arg);
    $this->expectOutputRegex('/' . __FUNCTION__ . "\($arg\)/");
  }
}
 
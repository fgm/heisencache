<?php
/**
 * @file
 * Test the EventEmitter class.
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache\tests;

use OSInet\Heisencache\EventEmitter;

class EventEmitterTest extends \PHPUnit_Framework_TestCase {

  const SUBSCRIBER_CLASS = 'OSInet\Heisencache\DebugSubscriber';
  const CHANNEL = "some channel";

  /**
   * @param array $events
   * @param null $class
   *
   * @return \OSInet\Heisencache\tests\MockEventSubscriberInterface
   */
  protected function getMockSubscriber(array $events, $class = NULL) {
    static $sequence = 0;

    if (!isset($class)) {
      $class = "MockSubscriber$sequence";
      $sequence++;
    }

    $subscriber = $this->getMock(self::SUBSCRIBER_CLASS, $events, array(), $class, FALSE);
    return $subscriber;
  }

  public function testOnSingleSubscriberSingleEvent() {
    $event1 = 'event1';
    $emitter = new EventEmitter();

    $subscriber = $this->getMockSubscriber(array($event1));

    try {
      $emitter->on($event1, $subscriber);
    }
    catch (\Exception $e) {
      $this->fail('Passing a subscriber to on() does not throw an exception.');
    }

    $actual = $emitter->getSubscribersByEventName($event1);
    $this->assertInternalType('array', $actual, "getSubscribersByEventName() returns an array.");
    $this->assertEquals(count($actual), 1, "Exactly 1 subscriber returned for event.");
    $this->assertEquals(reset($actual), $subscriber, "Single subscriber on event is returned correctly.");

    try {
      $emitter->on($event1, $subscriber);
    }
    catch (\Exception $e) {
      $this->fail('Passing the same subscriber twice to on() does not throw an exception.');
    }
  }

  public function testOnSingleSubscriberInvalidEvent() {
    $event1 = 'event1';
    $event2 = 'event2';
    $emitter = new EventEmitter();

    $subscriber = $this->getMockSubscriber(array($event1));

    try {
      $emitter->on($event2, $subscriber);
      $this->fail('Passing a subscriber to on() throws an exception.');
    }
    catch (\InvalidArgumentException $e) {
    }
  }

  public function testOnSingleSubscriberTwoEvents() {
    $event1 = 'event1';
    $event2 = 'event2';
    $emitter = new EventEmitter();

    $subscriber = $this->getMockSubscriber(array($event1, $event2));

    try {
      $emitter->on($event1, $subscriber);
    }
    catch (\Exception $e) {
      $this->fail('Passing a subscriber to on() does not throw an exception.');
      }

    $actual = $emitter->getSubscribersByEventName($event1);
    $this->assertInternalType('array', $actual, "getSubscribersByEventName() returns an array.");
    $this->assertEquals(count($actual), 1, "Exactly 1 subscriber returned for first event.");
    $this->assertEquals(reset($actual), $subscriber, "Single subscriber on first event is returned correctly.");

    try {
      $emitter->on($event2, $subscriber);
    }
    catch (\Exception $e) {
      echo "Exception: " . $e->getMessage() . "\n";
      $this->fail('Passing the same subscriber to on() for a second event does not throw an exception.');
    }
    $actual = $emitter->getSubscribersByEventName($event2);
    $this->assertInternalType('array', $actual, "getSubscribersByEventName() returns an array.");
    $this->assertEquals(count($actual), 1, "Exactly 1 subscriber returned for second event.");
    $this->assertEquals(reset($actual), $subscriber, "Single subscriber on second event is returned correctly.");
  }

  public function testOnTwoSubscribersSingleEvent() {
    $event1 = 'event1';
    $emitter = new EventEmitter();

    $sub1 = $this->getMockSubscriber(array($event1));
    $sub2 = $this->getMockSubscriber(array($event1));

    try {
      $emitter->on($event1, $sub1);
    }
    catch (\Exception $e) {
      $this->fail('Passing a subscriber to on() does not throw an exception.');
    }

    $actual = $emitter->getSubscribersByEventName($event1);
    $this->assertInternalType('array', $actual, "getSubscribersByEventName() returns an array.");
    $this->assertEquals(count($actual), 1, "Exactly 1 subscriber returned for event.");
    $this->assertEquals(reset($actual), $sub1, "Single subscriber on first event is returned correctly.");

    try {
      $emitter->on($event1, $sub2);
    }
    catch (\Exception $e) {
      $this->fail('Passing a different subscriber to on() for the same event does not throw an exception.');
    }

    $actual = $emitter->getSubscribersByEventName($event1);
    $this->assertInternalType('array', $actual, "getSubscribersByEventName() returns an array.");
    $this->assertEquals(count($actual), 2, "Exactly 2 subscribers returned for event.");

    $this->assertTrue(in_array($sub1, $actual), "First subscriber on event is returned correctly.");
    $this->assertTrue(in_array($sub2, $actual), "Second subscriber on event is returned correctly.");
  }

  public function testRegister() {
    $event1 = 'event1';
    $event2 = 'event2';
    $events = array($event1, $event2);
    $mocked = array_merge($events, array('getSubscribedEvents'));

    $subscriber = $this->getMockSubscriber($mocked);
    $subscriber->expects($this->once())
      ->method('getSubscribedEvents')
      ->will($this->returnValue($events));

    $emitter = new EventEmitter();
    $emitter->register($subscriber);

    foreach ($events as $eventName) {
      $actual = $emitter->getSubscribersByEventName($eventName);
      $this->assertTrue(in_array($subscriber, $actual));
    }
  }

  public function testEmitHappy() {
    $event1 = 'event1';
    $emitter = new EventEmitter();

    $subscriber = $this->getMockSubscriber(array($event1));
    $subscriber->expects($this->once())
      ->method($event1);
    $emitter->on($event1, $subscriber);

    $emitter->emit($event1, self::CHANNEL);
  }

  /**
   * Test an event emitted without any listener at all.
   */
  public function testEmitSadNoSubscriber() {
    $event1 = 'event1';
    $emitter = new EventEmitter();

    // No subscriber: no one should be notified.
    $notified = $emitter->emit($event1, self::CHANNEL);
    $this->assertEquals(0, $notified);
  }

  /**
   * Test an event emitted without any listener on the event.
   */
  public function testEmitSadOtherSubscriber() {
    $event1 = 'event1';
    $event2 = 'event2';
    $emitter = new EventEmitter();

    $subscriber = $this->getMockSubscriber(array($event1));
    // Not called because this event is not the one being emitted.
    $subscriber->expects($this->never())
      ->method($event1);
    // Not called because this event is not the one being subscribed.
    $subscriber->expects($this->never())
      ->method($event2);

    // Subscriber is on event1, so no one should be notified on emit(event2).
    $listener_count = $emitter->on($event1, $subscriber);
    $this->assertEquals(1, $listener_count);

    $notified_count = $emitter->emit($event2, self::CHANNEL);
    $this->assertEquals(0, $notified_count);
  }
}

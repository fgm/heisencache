<?php
/**
 * @file
 * Unit tests for BaseEventSubscriber
 *
 * @author: marand
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache\tests;

class BaseEventSubscriberTest extends \PHPUnit_Framework_TestCase {

  public function testRemoveEvent() {
    $event1 = 'event1';
    $events = array($event1);

    $mock = $this->getMockForAbstractClass('OSInet\Heisencache\BaseEventSubscriber');
    $mock->addEvent($event1);

    $actual = $mock->getSubscribedEvents();
    $this->assertEquals($events, $actual);

    $mock->removeEvent($event1);
    $actual = $mock->getSubscribedEvents();
    $this->assertEquals(array(), $actual);
  }
}

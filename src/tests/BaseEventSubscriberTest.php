<?php
/**
 * @file
 * Unit tests for BaseEventSubscriber
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache\tests;

class BaseEventSubscriberTest extends \PHPUnit_Framework_TestCase {

  public function testRemoveEvent() {
    $event1 = 'event1';
    $events = array($event1);

    /** @var \OSInet\Heisencache\tests\MockEventSubscriberInterface $mock */
    $mock = $this->getMockForAbstractClass('OSInet\Heisencache\BaseEventSubscriber');
    $mock->addEvent($event1);

    $actual = $mock->getSubscribedEvents();
    $this->assertEquals($events, $actual);

    $mock->removeEvent($event1);
    $actual = $mock->getSubscribedEvents();
    $this->assertEquals(array(), $actual);
  }
}

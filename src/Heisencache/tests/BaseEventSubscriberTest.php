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

namespace Drupal\heisencache\tests;

use PHPUnit\Framework\TestCase;

class BaseEventSubscriberTest extends TestCase {

  public function testRemoveEvent() {
    $event1 = 'event1';
    $events = array($event1);

    /** @var \Drupal\heisencache\tests\MockEventSubscriberInterface $mock */
    $mock = $this->getMockForAbstractClass('Drupal\heisencache\BaseEventSubscriber');
    $mock->addEvent($event1);

    $actual = $mock->getSubscribedEvents();
    $this->assertEquals($events, $actual);

    $mock->removeEvent($event1);
    $actual = $mock->getSubscribedEvents();
    $this->assertEquals(array(), $actual);
  }
}

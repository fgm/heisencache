<?php

declare(strict_types=1);

namespace Drupal\heisencache\tests;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for BaseEventSubscriber
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
class BaseEventSubscriberTest extends TestCase {

  public function testRemoveEvent() {
    $event1 = 'event1';
    $events = [$event1];

    /** @var \Drupal\heisencache\tests\MockEventSubscriberInterface $mock */
    $mock = $this->getMockForAbstractClass('Drupal\heisencache\BaseEventSubscriber');
    $mock->addEvent($event1);

    $actual = $mock->getSubscribedEvents();
    $this->assertEquals($events, $actual);

    $mock->removeEvent($event1);
    $actual = $mock->getSubscribedEvents();
    $this->assertEquals([], $actual);
  }

}

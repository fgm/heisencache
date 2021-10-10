<?php

namespace Drupal\heisencache\EventSubscriber;

/**
 * The base event subscriber class.
 *
 * @copyright (c) 2015-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
abstract class BaseEventSubscriber implements EventSubscriberInterface {

  protected array $subscribedEvents = [];

  public function addEvent($eventName) {
    $this->subscribedEvents[$eventName] = TRUE;
  }

  public function getSubscribedEvents(): array {
    return array_keys($this->subscribedEvents);
  }

  public function removeEvent($eventName) {
    unset($this->subscribedEvents[$eventName]);
  }

}

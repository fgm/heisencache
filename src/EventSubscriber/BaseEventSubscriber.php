<?php
/**
 * @file
 *   EventListener.php
 *
 * @copyright (c) 2015-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace Drupal\heisencache\EventSubscriber;


abstract class BaseEventSubscriber implements EventSubscriberInterface {

  protected $subscribedEvents = array();

  public function addEvent($eventName) {
    $this->subscribedEvents[$eventName] = TRUE;
  }

  public function getSubscribedEvents() {
    return array_keys($this->subscribedEvents);
  }

  public function removeEvent($eventName) {
    unset($this->subscribedEvents[$eventName]);
  }
}

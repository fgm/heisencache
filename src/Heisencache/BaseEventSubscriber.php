<?php
/**
 * @file
 *   EventListener.php
 *
 * @author: marand
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache;


abstract class BaseEventSubscriber implements EventSubscriberInterface {

  protected $events = array();

  public function addEvent($eventName) {
    $this->events[$eventName] = TRUE;
  }

  public function getEvents() {
    return array_keys($this->events);
  }

  public function removeEvent($eventName) {
    unset($this->events[$eventName]);
  }
}

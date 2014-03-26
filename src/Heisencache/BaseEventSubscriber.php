<?php
/**
 * @file
 *   EventListener.php
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache;


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

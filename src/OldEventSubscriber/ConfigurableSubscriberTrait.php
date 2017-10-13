<?php

namespace Drupal\heisencache\EventSubscriber;

trait ConfigurableSubscriberTrait {

  protected static $subscribedEvents = [];

  public function addEvent($eventName) {
    static::$subscribedEvents[$eventName] = TRUE;
  }

  public function removeEvent($eventName) {
    unset(static::$subscribedEvents[$eventName]);
  }

}

<?php

namespace Drupal\heisencache\EventSubscriber;


trait EventSourceTrait {

  protected static $emittedEvents = [];

  /**
   * {@inheritdoc}
   */
  public static function getEmittedEvents() {
    return static::$emittedEvents;
  }
}

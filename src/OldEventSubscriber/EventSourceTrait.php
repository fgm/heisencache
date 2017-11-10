<?php

namespace Drupal\heisencache\EventSubscriber;

trait EventSourceTrait {

  protected static $emittedEvents = [];

  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * {@inheritdoc}
   */
  public static function getEmittedEvents() {
    return static::$emittedEvents;
  }

  public function dispatcher() {
    if (!isset($this->eventDispatcher)) {
      $this->eventDispatcher = \Drupal::service('event_dispatcher');
    }

    return $this->eventDispatcher;
  }
}

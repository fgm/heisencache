<?php

namespace Drupal\heisencache\EventSubscriber;

use Drupal\heisencache\Event\EventSourceInterface;

abstract class EventSourceSubscriber extends BaseEventSubscriber implements EventSourceInterface {

  protected static $emittedEvents = [];

  /**
   * @var \Drupal\heisencache\EventSubscriber\EventEmitter
   */
  protected $emitter;

  public function __construct(EventEmitter $emitter) {
    $this->emitter = $emitter;
  }

  /**
   * {@inheritdoc}
   */
  public static function getEmittedEvents() {
    return static::$emittedEvents;
  }
}

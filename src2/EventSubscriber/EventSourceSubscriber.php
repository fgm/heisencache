<?php

namespace Drupal\heisencache\EventSubscriber;

use Drupal\heisencache\Event\EventSourceInterface;

/**
 * An event subscriber which is also an event source.
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
class EventSourceSubscriber extends BaseEventSubscriber implements EventSourceInterface {

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

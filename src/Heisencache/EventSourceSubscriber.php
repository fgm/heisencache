<?php
/**
 * @file
 * A subscriber which also emits synthetic events.
 *
 * @author: marand
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache;


class EventSourceSubscriber extends BaseEventSubscriber implements EventSourceInterface {

  protected static $emittedEvents = array();
  /**
   * @var \OSInet\Heisencache\EventEmitter
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

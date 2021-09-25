<?php
/**
 * @file
 * A subscriber which also emits synthetic events.
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace Drupal\heisencache;


use Drupal\heisencache\Event\EventSourceInterface;

class EventSourceSubscriber extends BaseEventSubscriber implements EventSourceInterface {

  protected static $emittedEvents = array();
  /**
   * @var \Drupal\heisencache\EventEmitter
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

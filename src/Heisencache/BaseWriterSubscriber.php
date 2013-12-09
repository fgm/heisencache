<?php
/**
 * @file
 *   BaseWriterSubscriber.php
 *
 * @author: marand
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache;


abstract class BaseWriterSubscriber extends BaseEventSubscriber {

  /**
   * @var array
   *   Array of events triggered in this page cycle.
   */
  protected $history;

  public function __construct(array $events = NULL) {
    if (!isset($events)) {
      $events = Cache::getEmittedEvents();
    }
    foreach ($events as $eventName) {
      $this->addEvent($eventName);
    }
    $this->history = array();
  }

  /**
   * Default handler invoked for all events except shutdown.
   *
   * @see WatchdogWriterSubscriber;;onShutdown()
   *
   * @param string $eventName
   * @param array $args
   */
  public function genericCall($eventName, $args) {
    if (strpos($eventName, 'before') !== 0 && strpos($eventName, 'after') !== 0) {
      //echo "<p>" . __CLASS__ . "::$eventName(" . $args[0] . ")</p>\n";
    }
    $this->history[] = array($eventName, $args);
  }

  /**
   * on() will accept ANY event for this subscriber, but only handle ours.
   *
   * @param string $eventName
   * @param array $args
   *
   * @throws \InvalidArgumentException
   */
  public function __call($eventName, $args) {
    if (!in_array($eventName, $this->subscribedEvents)) {
      throw new \InvalidArgumentException("Unsupported event $eventName");
    }
    else {
      $this->genericCall($eventName, $args);
    }
  }
}
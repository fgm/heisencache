<?php

declare(strict_types=1);

namespace Drupal\heisencache\EventSubscriber;

use Drupal\heisencache\Cache\Cache;

/**
 * A base class for subscribers writing results.
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
abstract class BaseWriterSubscriber extends BaseEventSubscriber {

  /**
   * @var array
   *   Array of events triggered in this page cycle.
   */
  protected array $history;

  /**
   * @var bool
   *   Echo generalCall() call information for debugging purposes..
   */
  protected bool $showGenericCalls = FALSE;

  public function __construct(array $events = NULL) {
    if (!isset($events)) {
      $events = Cache::getEmittedEvents();
    }
    foreach ($events as $eventName) {
      $this->addEvent($eventName);
    }
    $this->history = [];
  }

  /**
   * Default handler invoked for all events except shutdown.
   *
   * @param string $eventName
   * @param array $args
   *
   * @see \Drupal\heisencache\EventSubscriber\WatchdogWriterSubscriber::onShutdown()
   *
   */
  public function genericCall(string $eventName, array $args) {
    if ($this->showGenericCalls
      && strpos($eventName, 'before') !== 0
      && strpos($eventName, 'after') !== 0
    ) {
      echo "<p>" . __CLASS__ . "::$eventName(" . $args[0] . ")</p>\n";
    }

    $this->history[] = [$eventName, $args];
  }

  /**
   * on() will accept ANY event for this subscriber, but only handle ours.
   *
   * @param string $eventName
   * @param array $args
   *
   * @throws \InvalidArgumentException
   */
  public function __call(string $eventName, array $args) {
    if (!in_array($eventName, $this->subscribedEvents)) {
      throw new \InvalidArgumentException("Unsupported event $eventName");
    }
    else {
      $this->genericCall($eventName, $args);
    }
  }

  public function setDebugCalls(bool $showGenericCalls = FALSE) {
    $this->showGenericCalls = $showGenericCalls;
  }

}

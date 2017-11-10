<?php

namespace Drupal\heisencache\EventSubscriber;

use Drupal\heisencache\Cache\InstrumentedBin;
use Drupal\heisencache\Exception\InvalidArgumentException;

abstract class WriterBase extends ConfigurableListenerBase implements TerminateWriterInterface {

  /**
   * @var array
   *   Array of events triggered in this page cycle.
   */
  protected $history;

  /**
   * @var bool
   *   Echo generalCall() call information for debugging purposes..
   */
  protected $showGenericCalls;

  /**
   * BaseWriter constructor.
   *
   * @param array|null $events
   */
  public function __construct($events = []) {
    if (!isset($events)) {
      $events = InstrumentedBin::getEmittedEvents();
    }
    foreach ($events as $eventName) {
      $this->addEvent($eventName);
    }
    $this->history = array();
  }

  /**
   * Default handler invoked for all events except terminate.
   *
   * @see WatchdogWriter::onTerminate()
   *
   * @param string $eventName
   * @param array $args
   */
  public function genericCall($eventName, $args) {
    if ($this->showGenericCalls && strpos($eventName, 'before') !== 0 && strpos($eventName, 'after') !== 0) {
      echo "<p>" . __CLASS__ . "::$eventName(" . $args[0] . ")</p>\n";
    }

    $this->history[] = array($eventName, $args);
  }

  /**
   * on() will accept ANY event for this subscriber, but only handle ours.
   *
   * @param string $eventName
   * @param array $args
   *
   * @throws \Drupal\heisencache\Exception\InvalidArgumentException
   */
  public function __call($eventName, $args) {
    if (!in_array($eventName, $this->subscribedEvents)) {
      throw new InvalidArgumentException("Unsupported event $eventName");
    }
    else {
      $this->genericCall($eventName, $args);
    }
  }

  public function setDebugCalls($showGenericCalls = FALSE) {
    $this->showGenericCalls = $showGenericCalls;
  }
}

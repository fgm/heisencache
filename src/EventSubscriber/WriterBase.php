<?php

namespace Drupal\heisencache\EventSubscriber;

use Drupal\heisencache\Cache\InstrumentedBin;
use Drupal\heisencache\Event\EventBase;
use Drupal\heisencache\Event\EventInterface;
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
    parent::__construct($events);
    $this->history = [];
  }

  /**
   * Default handler invoked for all events except terminate.
   *
   * @see WatchdogWriterSubscriber::onTerminate()
   *
   * @param string $eventName
   * @param array $args
   */
  public function genericCall($eventName, $args) {
    $event = $args[0];
    $data = ($event instanceof EventBase) ? $event->data() : '(CoreEvent)';
    if ($this->showGenericCalls && strpos($eventName, 'before') !== 0 && strpos($eventName, 'after') !== 0) {
      echo "<p>" . __CLASS__ . "::$eventName(" . $data[0] . ")</p>\n";
    }

    $this->history[] = array($eventName, $data);
  }

  public function isListenedTo(string $event_name) {
    return in_array($event_name, $this->listenedEvents);
  }

  /**
   * on() will accept ANY event for this subscriber, but only handle ours.
   *
   * @param string $method
   * @param array $args
   *
   */
  public function __call($method, $args) {
    list(, $eventName, ) = $args;
    if (!$this->isListenedTo($eventName)) {
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

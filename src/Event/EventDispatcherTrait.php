<?php

namespace Drupal\heisencache\Event;

/**
 * Facade for Symfony dispatcher.
 *
 * @package Drupal\heisencache\Event
 */
trait EventDispatcherTrait {

  /**
   * The event.dispatcher service.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * Dispatch a Heisencache event.
   *
   * @param \Drupal\heisencache\Event\EventBase $event
   *   This should be a EventInterface instead, but the Symfony dispatcher
   *   type-hints on a concrete Event instead of an interface.
   */
  protected function dispatch(EventBase $event) {
    $this->dispatcher->dispatch($event->name(), $event);
  }

}

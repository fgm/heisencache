<?php

declare(strict_types=1);

namespace Drupal\heisencache\Event;

/**
 * Facade for Symfony dispatcher.
 *
 * @copyright (c) 2015-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
trait EventDispatcherTrait {

  /**
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

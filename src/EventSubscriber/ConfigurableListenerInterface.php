<?php

namespace Drupal\heisencache\EventSubscriber;

/**
 * An interface for subscribers accepting a configurable list of events.
 *
 * Implementers MUST also implement one method for each subscribed event.
 *
 * @package Drupal\heisencache\EventSubscriber
 */
interface ConfigurableListenerInterface {

  const LISTENER_TAG = 'heisencache_listener';

  /**
   * Add an event to the service listening list.
   *
   * @param string $eventName
   *   The short name for Heisencache events
   */
  public function addEvent(string $eventName): void;

  /**
   * Return the events to which the service is listening.
   *
   * @return array
   */
  public function getListenedEvents(): array;

  /**
   * Remove an event from the service listening list.
   *
   * @param string $eventName
   */
  public function removeEvent($eventName): void;

}

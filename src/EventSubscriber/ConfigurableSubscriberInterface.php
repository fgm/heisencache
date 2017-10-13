<?php

namespace Drupal\heisencache\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface as CoreEventSubscriberInterface;

/**
 * An interface for subscribers accepting a configurable list of events.
 *
 * Implementers MUST also implement one method for each subscribed event.
 *
 * @package Drupal\heisencache\EventSubscriber
 */
interface ConfigurableSubscriberInterface extends CoreEventSubscriberInterface {

  /**
   * @param string $eventName
   *
   * @return void
   */
  public static function addEvent($eventName);

  /**
   * @param string $eventName
   *
   * @return void
   */
  public static function removeEvent($eventName);
}

<?php

declare(strict_types=1);

namespace Drupal\heisencache\EventSubscriber;

/**
 * Interface EventSubscriberInterface.
 *
 * Implementers MUST also implement one method for each subscribed event.
 *
 * @package Drupal\heisencache\EventSubscriber
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
interface EventSubscriberInterface {

  /**
   * @param string $eventName
   *
   * @return void
   */
  public function addEvent(string $eventName);

  /**
   * @return string[]
   */
  public function getSubscribedEvents(): array;

  /**
   * @param string $eventName
   *
   * @return void
   */
  public function removeEvent(string $eventName);

}

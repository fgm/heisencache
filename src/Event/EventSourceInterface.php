<?php

declare(strict_types=1);

namespace Drupal\heisencache\Event;

/**
 * EventSourceInterface is the interface shared by classes emitting events.
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

interface EventSourceInterface {
  /**
   * @return string[]
   */
  public static function getEmittedEvents(): array;
}

<?php
/**
 * @file
 *   EventSourceInterface.php
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache;


interface EventSourceInterface {
  /**
   * @return string[]
   */
  public static function getEmittedEvents();
}
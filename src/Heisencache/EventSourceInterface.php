<?php
/**
 * @file
 *   EventSourceInterface.php
 *
 * @author: marand
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
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
<?php
/**
 * @file
 *   EventSourceInterface.php
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace Drupal\heisencache\EventSubscriber;


interface EventSourceInterface {
  /**
   * @return string[]
   */
  public static function getEmittedEvents();
}

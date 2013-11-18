<?php
/**
 * @file
 *   EventSubscriberInterface.php
 *
 * @author: marand
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache;

/**
 * Interface EventSubscriberInterface.
 *
 * Implementers MUST also implement one method for each subscribed event.
 *
 * @package OSInet\Heisencache
 */
interface EventSubscriberInterface {
  /**
   * @param string $eventName
   *
   * @return void
   */
  public function addEvent($eventName);

  /**
   * @return string[]
   */
  public function getEvents();

  /**
   * @param string $eventName
   *
   * @return void
   */
  public function removeEvent($eventName);
}

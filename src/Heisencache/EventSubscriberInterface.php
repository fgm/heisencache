<?php
/**
 * @file
 *   EventSubscriberInterface.php
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
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
  public function getSubscribedEvents();

  /**
   * @param string $eventName
   *
   * @return void
   */
  public function removeEvent($eventName);
}

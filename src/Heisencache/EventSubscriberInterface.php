<?php
/**
 * @file
 *   EventSubscriberInterface.php
 *
 * @author: marand
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
   * @param string $name
   *
   * @return void
   */
  public function addEvent($name);

  /**
   * @return string[]
   */
  public function getEvents();
}

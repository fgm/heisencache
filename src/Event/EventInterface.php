<?php
/**
 * @file
 * EventInterface.php
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2015 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace Drupal\heisencache\Event;

/**
 * Interface EventInterface is implemented by all Heisencache events.
 *
 * It assumes events may be recycled in a dual pre/post triggering.
 *
 * @package Drupal\heisencache\Event
 */
interface EventInterface {
  /**
   * Event happening before operation
   */
  const PRE = 'pre';

  /**
   * Event created within operation
   */
  const IN = 'in';

  /**
   * Event happening after operation
   */
  const POST = 'post';

  /**
   * @return string
   *   The event kind: pre|in|post
   */
  public function kind();

  /**
   * @return string
   *   The event name.
   */
  public function name();

  /**
   * Sets the event kind to "post", allow it to propagate, and returns it.
   * Events are created as "pre" by default.
   *
   * @return $this
   */
  public function setPost();

}

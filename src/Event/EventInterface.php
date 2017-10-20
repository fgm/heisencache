<?php

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
   * Event has been dispatched before operation.
   */
  const PRE = 'before';

  /**
   * Event has been dispatched within operation.
   */
  const IN = 'on';

  /**
   * Event has been dispatched after operation.
   */
  const POST = 'after';

  /**
   * Getter for the event kind.
   *
   * @return string
   *   The event kind: pre|in|post
   */
  public function kind();

  /**
   * Getter for the event name.
   *
   * @return string
   *   The event name.
   */
  public function name();

  /**
   * Sets the event kind to "post", allow it to propagate, and returns it.
   *
   * Events are created as "pre" by default.
   *
   * @return $this
   */
  public function setPost();

}

<?php

namespace Drupal\heisencache\Event;

use Drupal\heisencache\HeisencacheServiceProvider as H;

/**
 * Interface EventInterface is implemented by all Heisencache events.
 *
 * It assumes events may be recycled in a dual pre/post triggering.
 *
 * @package Drupal\heisencache\Event
 */
interface EventInterface {

  /**
   * Event has been dispatched during operation.
   */
  const IN = 'in';

  /**
   * Event has been dispatched before operation.
   */
  const PRE = 'before';

  /**
   * Event has been dispatched after operation.
   */
  const POST = 'after';

  /**
   * The base event names.
   *
   * Each of these events may be triggered as "in" or "after"|"before".
   */
  const BACKEND_CONSTRUCT = '_backend_construct';

  const BACKEND_DELETE = '_backend_delete';
  const BACKEND_DELETE_ALL = '_backend_delete_all';
  const BACKEND_DELETE_MULTIPLE = '_backend_delete_multiple';

  const BACKEND_GARBAGE_COLLECTION = '_backend_garbage_collection';

  const BACKEND_GET = '_backend_get';
  const BACKEND_GET_MULTIPLE = '_backend_get_multiple';

  const BACKEND_INVALIDATE = '_backend_invalidate';
  const BACKEND_INVALIDATE_ALL = '_backend_invalidate_all';
  const BACKEND_INVALIDATE_MULTIPLE = '_backend_invalidate_multiple';

  const BACKEND_SET = '_backend_set';
  const BACKEND_SET_MULTIPLE = '_backend_set_multiple';

  const REMOVE_BIN = '_remove_bin';

  /**
   * The list of Heisencache events, for "wildcard" listeners.
   *
   * @see \Drupal\heisencache\EventSubscriber\DebugSubscriber
   * @see \Drupal\heisencache\EventSubscriber\WriterBase
   */
  const EVENTS = [
    self::IN . '_' . self::BACKEND_CONSTRUCT,

    self::PRE . '_' . self::BACKEND_DELETE,
    self::POST . '_' . self::BACKEND_DELETE,
    self::PRE . '_' . self::BACKEND_DELETE_ALL,
    self::POST . '_' . self::BACKEND_DELETE_ALL,
    self::PRE . '_' . self::BACKEND_DELETE_MULTIPLE,
    self::POST . '_' . self::BACKEND_DELETE_MULTIPLE,

    self::PRE . '_' . self::BACKEND_GARBAGE_COLLECTION,
    self::POST . '_' . self::BACKEND_GARBAGE_COLLECTION,

    self::PRE . '_' . self::BACKEND_GET,
    self::POST . '_' . self::BACKEND_GET,
    self::PRE . '_' . self::BACKEND_GET_MULTIPLE,
    self::POST . '_' . self::BACKEND_GET_MULTIPLE,

    self::PRE . '_' . self::BACKEND_INVALIDATE,
    self::POST . '_' . self::BACKEND_INVALIDATE,
    self::PRE . '_' . self::BACKEND_INVALIDATE_ALL,
    self::POST . '_' . self::BACKEND_INVALIDATE_ALL,
    self::PRE . '_' . self::BACKEND_INVALIDATE_MULTIPLE,
    self::POST . '_' . self::BACKEND_INVALIDATE_MULTIPLE,

    self::PRE . '_' . self::BACKEND_SET,
    self::POST . '_' . self::BACKEND_SET,
    self::PRE . '_' . self::BACKEND_SET_MULTIPLE,
    self::POST . '_' . self::BACKEND_SET_MULTIPLE,

    self::PRE . '_' . self::REMOVE_BIN,
    self::POST . '_' . self::REMOVE_BIN,
  ];

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

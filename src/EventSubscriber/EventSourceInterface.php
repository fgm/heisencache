<?php

namespace Drupal\heisencache\EventSubscriber;

/**
 * Interface EventSourceInterface allows event sources to list events they emit.
 *
 * @package Drupal\heisencache\EventSubscriber
 */
interface EventSourceInterface {

  const EMITTER_TAG = 'heisencache_emitter';

  /**
   * Return the names of the events emitted by this source.
   *
   * @return string[]
   *   An array of event names.
   */
  public static function getEmittedEvents();

}

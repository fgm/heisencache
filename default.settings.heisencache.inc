<?php

/**
 * @file
 * Configure the Heisencache cache proxy: instantiate subscribers.
 *
 * Copy this file to the site settings directory for Heisencache to pick it up,
 * and edit it to fit your needs.
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

declare(strict_types=1);

// Operate in the plugin namespace.
namespace Drupal\heisencache;

$emitter = Config::instance()->getEmitter();

$emitter
  ->register(new MissSubscriber($emitter))
  ->register(new PerformanceSubscriber($emitter))

  /* MissSubscriber is also an event source, like Cache, we can use its events. */

  /*
  Sample configuration listening to everything (big info volume per page).
   ->register(new WatchdogWriterSubscriber(array_merge(
      Cache::getEmittedEvents(),
      MissSubscriber::getEmittedEvents()
    )));
   */

  /*

  Sample Watchdog-based configuration to record cache misses and performance.
  Note: WatchdogWriterSubscriber will not record anything on cached pages.
    ->register(new WatchdogWriterSubscriber(array_merge(
      array('onShutdown'),
      MissSubscriber::getEmittedEvents(),
      PerformanceSubscriber::getEmittedEvents()
    )));
   */


  // Sample SQL-based configuration to record cache misses, even on pages served
  // from the Drupal page cache.
  // Note: SqlWriterSubscriber needs the Heisencache module to be enabled.
  ->register(new SqlWriterSubscriber(array_merge(
    ['onShutdown'],
    MissSubscriber::getEmittedEvents()
  )));

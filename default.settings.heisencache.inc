<?php
/**
 * @file
 * Defines the configuration of the Heisencache cache proxy:
 * - instantiate subscribers
 *
 * Copy this file to the site settings directory for Heisencache to pick it up,
 * and edit it to fit your needs.
 *
 * @author: marand
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

// Operate in the plugin namespace
namespace OSInet\Heisencache;

$emitter = Config::instance()->getEmitter();

$emitter
  ->register(new MissSubscriber($emitter))
  ->register(new PerformanceSubscriber($emitter))

  // MissSubscriber is also an event source, like Cache, so we can use its events.

// Sample configuration listening to everything. > insert 1 Mb per page.
//  ->register(new WatchdogWriterSubscriber(array_merge(
//    Cache::getEmittedEvents(),
//    MissSubscriber::getEmittedEvents()
//  )));

  // Sample configuration to just record cache misses.
  ->register(new WatchdogWriterSubscriber(array_merge(
    array('onShutdown'),
    MissSubscriber::getEmittedEvents(),
    PerformanceSubscriber::getEmittedEvents()
  )));

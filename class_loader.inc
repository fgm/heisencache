<?php

/**
 * @file
 * Heisencache: class loader.
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

/**
 * Load required files without using an autoloader.
 *
 * - When loading for unit tests, start by loading Drupal cache API.
 */
function heisencache_require() {
  /* When running unit tests, we need to load cache.inc , otherwise it will be
    included by the Drupal bootstrap process. */
  if (defined('HEISENCACHE_DRUPAL_BASE')) {
    $drupal_base = realpath(HEISENCACHE_DRUPAL_BASE);
    if (empty($drupal_base)) {
      throw new \Exception('Invalid HEISENCACHE_DRUPAL_BASE path.');
    }

    // Needed for the CACHE_* constants.
    /** @noinspection PhpIncludeInspection */
    require_once "$drupal_base/includes/bootstrap.inc";

    // Needed for the Cache API.
    /** @noinspection PhpIncludeInspection */
    require_once "$drupal_base/includes/cache.inc";
  }
  else {
    $drupal_base = NULL;
  }

  // Load cache-related classes.
  require_once __DIR__ . "/src/Heisencache/EventSourceInterface.php";
  require_once __DIR__ . "/src/Heisencache/EventSourceInterface.php";
  require_once __DIR__ . "/src/Heisencache/EventSubscriberInterface.php";
  require_once __DIR__ . "/src/Heisencache/BaseEventSubscriber.php";
  require_once __DIR__ . "/src/Heisencache/EventSourceSubscriber.php";
  require_once __DIR__ . "/src/Heisencache/EventEmitter.php";
  require_once __DIR__ . "/src/Heisencache/Cache.php";
  require_once __DIR__ . "/src/Heisencache/Config.php";

  // Load basic subscribers.
  require_once __DIR__ . "/src/Heisencache/DebugSubscriber.php";
  require_once __DIR__ . "/src/Heisencache/MissSubscriber.php";
  require_once __DIR__ . "/src/Heisencache/PerformanceSubscriber.php";
  require_once __DIR__ . "/src/Heisencache/WriteSubscriber.php";

  // Load writer subscribers.
  require_once __DIR__ . "/src/Heisencache/BaseWriterSubscriber.php";
  require_once __DIR__ . "/src/Heisencache/SqlWriterSubscriber.php";
  require_once __DIR__ . "/src/Heisencache/WatchdogWriterSubscriber.php";

  return $drupal_base;
}

heisencache_require();
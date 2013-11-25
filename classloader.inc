<?php

/**
 * Load required files without using an autoloader.
 *
 * - When loading for unit tests, start by loading Drupal cache API.
 */
function heisencache_require() {
  $srcDir = __DIR__ . '/src/Heisencache';

  /* When running unit tests, we need to load cache.inc , otherwise it will be
    included by the Drupal bootstrap process. */
  if (defined('HEISENCACHE_DRUPAL_BASE')) {
    $drupal_base = realpath(HEISENCACHE_DRUPAL_BASE);
    if (empty($drupal_base)) {
      throw new \Exception('Invalid HEISENCACHE_DRUPAL_BASE path.');
    }

    // Needed for the CACHE_* constants.
    require_once "$drupal_base/includes/bootstrap.inc";
    // Needed for the Cache API.
    require_once "$drupal_base/includes/cache.inc";
  }

  // Load cache-related classes.
  require_once "$srcDir/EventSubscriberInterface.php";
  require_once "$srcDir/BaseEventSubscriber.php";
  require_once "$srcDir/EventEmitter.php";
  require_once "$srcDir/Cache.php";
  require_once "$srcDir/Config.php";

  // Load basic subscribers.
  require_once "$srcDir/DebugSubscriber.php";
  require_once "$srcDir/MissSubscriber.php";
  require_once "$srcDir/WatchdogWriterSubscriber.php";
}

heisencache_require();
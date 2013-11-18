<?php
/**
 * @file
 *   cache.inc: override existing caches.
 *
 * @author: marand
 *
 * @license General Public License version 2 or later
 */

use OSInet\Heisencache\Config;

/**
 * Load required files without using an autoloader.
 */
function heisencache_require() {
  $src = __DIR__ . '/src/Heisencache';

  // Load cache-related classes.
  require_once "$src/EventSubscriberInterface.php";
  require_once "$src/BaseEventSubscriber.php";
  require_once "$src/EventEmitter.php";
  require_once "$src/Cache.php";
  require_once "$src/Config.php";

// Bring in overriding capability.
  require_once __DIR__ . '/heisencache_overrides.inc';
}

heisencache_require();
$GLOBALS['conf'] = Config::instance($GLOBALS['conf'])->override();

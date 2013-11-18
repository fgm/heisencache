<?php
/**
 * @file
 *   cache.inc: override existing caches.
 *
 * @author: marand
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

use OSInet\Heisencache\Config;

/**
 * Load required files without using an autoloader.
 */
function heisencache_require() {
  $srcDir = __DIR__ . '/src/Heisencache';

  // Load cache-related classes.
  require_once "$srcDir/EventSubscriberInterface.php";
  require_once "$srcDir/BaseEventSubscriber.php";
  require_once "$srcDir/EventEmitter.php";
  require_once "$srcDir/Cache.php";
  require_once "$srcDir/Config.php";
}

heisencache_require();
$config = Config::instance($GLOBALS['conf']);
$GLOBALS['conf'] = $config->override();
unset($config);

$settings_path = conf_path() . '/settings.heisencache.inc';
if (is_file($settings_path)) {
  require_once $settings_path;
}
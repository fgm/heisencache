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

require_once __DIR__ . '/classloader.inc';

function heisencache_setup($configuration) {
  $config = Config::instance();
  drupal_register_shutdown_function(array($config->getEmitter(), 'emit'), 'onShutdown', 'cache');
  return $config->override();
}

$GLOBALS['conf'] = heisencache_setup($GLOBALS['conf']);

$settings_path = conf_path() . '/settings.heisencache.inc';
if (is_file($settings_path)) {
  require_once $settings_path;
}

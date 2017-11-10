<?php

/**
 * @file
 *   cache.inc: override existing caches.
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2017 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

use OSInet\Heisencache\Config;

function heisencache_setup($configuration) {
  $config = Config::instance($configuration);
  drupal_register_shutdown_function(array($config->getEmitter(), 'emit'), 'onTerminate', 'cache');
  return $config->override();
}

$GLOBALS['conf'] = heisencache_setup($GLOBALS['conf']);

$settings_path = conf_path() . '/settings.heisencache.inc';
if (is_file($settings_path)) {
  /** @noinspection PhpIncludeInspection */
  require_once $settings_path;
}

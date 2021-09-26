<?php

/**
 * @file
 * Like cache.inc: override existing caches.
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

declare(strict_types=1);

use OSInet\Heisencache\Config;

require_once __DIR__ . '/class_loader.inc';

/**
 * Configure Heisencache.
 *
 * Invoke this function AFTER doing the standard cache configuration, so it can
 * override it.
 *
 * @param array $configuration
 *   Ras site $conf.
 *
 * @return array
 *   Adapted site $conf.
 */
function heisencache_setup(array $configuration): array {
  $config = Config::instance($configuration);
  drupal_register_shutdown_function(array($config->getEmitter(), 'emit'), 'onShutdown', 'cache');
  return $config->override();
}

$GLOBALS['conf'] = heisencache_setup($GLOBALS['conf']);

$settings_path = conf_path() . '/settings.heisencache.inc';
if (is_file($settings_path)) {
  // phpcs:ignore PhpIncludeInspection
  require_once $settings_path;
}

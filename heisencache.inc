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

namespace Drupal\heisencache;

/**
 * Dedicated autoloader.
 *
 * @param string $name
 *
 * @throws \Exception
 */
function heisencache_autoload(string $name): void {
  // This autoloader is dedicated to the module namespace.
  if (FALSE === strpos($name, __NAMESPACE__)) {
    return;
  }
  $base = __DIR__ . "/src";
  $shortName = substr($name, 1 + strlen(__NAMESPACE__)) . ".php";
  $split = explode("\\", $shortName);
  if (!is_array($split)) {
    throw new \Exception(
      st("Unexpected invalid class/interface/trait @name",
         ["@name" => $name]
      )
    );
  }
  array_unshift($split, $base);
  $fileName = implode(DIRECTORY_SEPARATOR, $split);
  require_once $fileName;
}

/**
 * Load required files: low-level core and PSR-4 autoloading for Heisencache.
 *
 * - When loading for unit tests, start by loading Drupal cache API manually.
 */
function heisencache_setup_autoload(): void {
  // When running unit tests, we need to load cache.inc , otherwise it will be
  // included by the Drupal bootstrap process.
  if (defined('HEISENCACHE_DRUPAL_BASE')) {
    $drupalBase = realpath(HEISENCACHE_DRUPAL_BASE);
    if (empty($drupalBase)) {
      throw new \Exception('Invalid HEISENCACHE_DRUPAL_BASE path.');
    }

    // Needed for the CACHE_* constants.
    /** @noinspection PhpIncludeInspection */
    require_once "$drupalBase/includes/bootstrap.inc";

    // Needed for the Cache API.
    /** @noinspection PhpIncludeInspection */
    require_once "$drupalBase/includes/cache.inc";
  }

  spl_autoload_register(heisencache_autoload::class);
}

function heisencache_load_settings() {
  $settings_path = conf_path() . '/settings.heisencache.inc';
  if (is_file($settings_path)) {
    // phpcs:ignore PhpIncludeInspection
    require_once $settings_path;
  }
}

/**
 * Configure Heisencache.
 *
 * Invoke this function AFTER doing the standard cache configuration, so it can
 * override it.
 *
 * @param array $configuration
 *   Raw site $conf.
 *
 * @return array
 *   Adapted site $conf.
 */
function heisencache_setup(array $configuration): array {
  heisencache_setup_autoload();
  heisencache_load_settings();

  $config = Config::instance($configuration);
  drupal_register_shutdown_function(
    [$config->getEmitter(), 'emit'],
    'onShutdown',
    'cache'
  );
  $ret = $config->override();
  return $ret;
}

$GLOBALS['conf'] = heisencache_setup($GLOBALS['conf']);

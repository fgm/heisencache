<?php

/**
 * @file
 * Default Views for Heisencache writers.
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

declare(strict_types=1);

/**
 * Implements hook_view_default_views().
 */
function heisencache_views_default_views() {
  $ret = array();
  $directory_iterator = new \DirectoryIterator(__DIR__);
  $regex_iterator = new \RegexIterator($directory_iterator, '/\.view\.inc$/');
  $schema = drupal_get_complete_schema();
  $view = [];
  foreach ($regex_iterator as $item) {
    unset($view);
    // phpcs:ignore PhpUndefinedMethodInspection
    $pathname = $item->getPathname();
    // phpcs:ignore PhpIncludeInspection
    include_once $pathname;
    if (!isset($view)) {
      // phpcs:ignore PhpUndefinedMethodInspection
      $view_arg = array(
        '@view' => $item->getFilename(),
      );
      drupal_set_message(t('@view does not seem to contain an exported view.', $view_arg), 'error');
      watchdog('heisencache', '@view does not seem to contain an exported view.', $view_arg, WATCHDOG_ERROR);
    }
    // Only provide tables based on enabled modules: notably dblog.
    elseif (isset($schema[$view->base_table]['module']) && module_exists($schema[$view->base_table]['module'])) {
      $ret[$view->name] = $view;
    }
  }

  return $ret;
}

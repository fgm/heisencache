<?php

/**
 * @file
 * Default Views for Heisencache writers.
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

function heisencache_views_default_views() {
  $ret = array();
  $directory_iterator = new \DirectoryIterator(__DIR__);
  $regex_iterator = new \RegexIterator($directory_iterator, '/\.view\.inc$/');
  $schema = drupal_get_complete_schema();
  foreach ($regex_iterator as $item) {
    unset($view);
    /** @noinspection PhpUndefinedMethodInspection */
    $pathname = $item->getPathname();
    /** @noinspection PhpIncludeInspection */
    include_once $pathname;
    if (!isset($view)) {
      /** @noinspection PhpUndefinedMethodInspection */
      $view_arg = array(
        '@view' => $item->getFilename(),
      );
      drupal_set_message(t('@view does not seem to contain an exported view.', $view_arg),  'error');
      watchdog('heisencache', '@view does not seem to contain an exported view.', $view_arg, WATCHDOG_ERROR);
    }
    // Only provide tables based on enabled modules: notably dblog.
    elseif (isset($schema[$view->base_table]['module']) && module_exists($schema[$view->base_table]['module'])) {
      $ret[$view->name] = $view;
    }
  }

  return $ret;
}
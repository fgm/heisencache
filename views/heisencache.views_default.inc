<?php

/**
 * @file
 * Default Views for Heisencache writers.
 *
 * @author: marand
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

function heisencache_views_default_views() {
  $ret = array();
  $diri = new \DirectoryIterator(__DIR__);
  $regi = new \RegexIterator($diri, '/\.view\.inc$/');
  $schema = drupal_get_complete_schema();
  foreach ($regi as $item) {
    unset($view);
    $pathname = $item->getPathname();
    include_once $pathname;
    if (!isset($view)) {
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
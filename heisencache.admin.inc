<?php

/**
 * @file
 * Heisencache administrative UI controllers.
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

function heisencache_page_reports() {
  $views = array_filter(views_get_all_views(), function ($view) {
    return isset($view->export_module) && $view->export_module == 'heisencache';
  });

  $items = array();
  foreach ($views as $view) {
    foreach ($view->display as $display) {
      if (empty($display->display_options['path']))  {
        continue;
      }

      $display_options = $display->display_options;
      $path = $display_options['path'];
      $title = $display_options['menu']['title'];
      $items[$title] = l($title, $path);
    }
  }

  ksort($items);
  $ret = array(
    '#theme' => 'item_list',
    '#items' => $items,
  );

  return $ret;
}

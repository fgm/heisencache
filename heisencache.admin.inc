<?php

/**
 * @file
 * Heisencache administrative UI controllers.
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

declare(strict_types=1);

/**
 * Page controller for admin/reports/heisencache.
 *
 * @return array
 *   A render array.
 */
function heisencache_page_reports(): array {
  if (!module_exists('views')) {
    return [
      '#markup' => t('<p><a href="!extend">Enable Views module</a> to used Heisencache Views report.</p>',
        [
          '!extend' => url('admin/modules#views'),
        ]),
    ];
  }
  $views = array_filter(views_get_all_views(), function ($view) {
    return isset($view->export_module) && $view->export_module == 'heisencache';
  });

  $items = [];
  foreach ($views as $view) {
    foreach ($view->display as $display) {
      if (empty($display->display_options['path'])) {
        continue;
      }

      $options = $display->display_options;
      $path = $options['path'];
      $title = $options['menu']['title'] ?? t('Heisencache @part', [
        '@part' => basename($path),
      ]);
      $items[$title] = l($title, $path);
    }
  }

  ksort($items);
  return [
    '#theme' => 'item_list',
    '#items' => $items,
  ];
}

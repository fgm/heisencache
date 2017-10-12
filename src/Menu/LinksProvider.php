<?php

namespace Drupal\heisencache\Menu;

use Drupal\heisencache\HeisencacheServiceProvider as H;
use Drupal\heisencache\Routing\RouteProvider;

/**
 * Class LinksProvider provides menu links.
 *
 * @package Drupal\heisencache\Menu
 */
class LinksProvider {

  /**
   * Implements hook_menu_links_discovered_alter().
   */
  public function alterLinks(array &$links) {
    $links[RouteProvider::REPORT] = [
      'description' => 'Get information about the caches in your system',
      'id' => RouteProvider::REPORT,
      'parent' => 'system.admin_reports',
      'provider' => H::MODULE,
      'route_name' => RouteProvider::REPORT,
      'title' => 'Heisencache',
    ];
  }

}

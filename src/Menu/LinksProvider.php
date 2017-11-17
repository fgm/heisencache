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
    $links[RouteProvider::CONFIG] = [
      'description' => 'Get information about the caches in your system',
      'id' => RouteProvider::CONFIG,
      'parent' => 'system.admin_config_development',
      'provider' => H::MODULE,
      'route_name' => RouteProvider::CONFIG,
      'title' => 'Heisencache',
    ];
  }

}

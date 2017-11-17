<?php

namespace Drupal\heisencache\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\heisencache\Controller\ConfigurationController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * The route provider for Heisencache.
 *
 * @package Drupal\heisencache\Routing
 */
class RouteProvider extends RouteSubscriberBase {
  const CONFIG = 'heisencache.config';

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $route = (new Route('/admin/config/heisencache'))->addDefaults([
      '_controller' => ConfigurationController::class . '::build',
      '_title' => 'Heisencache configuration report',
    ])->setRequirement('_permission', 'access site reports');
    $collection->add(self::CONFIG, $route);
  }

}

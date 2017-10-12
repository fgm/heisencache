<?php

namespace Drupal\heisencache\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\heisencache\Controller\ReportController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * The route provider for Heisencache.
 *
 * @package Drupal\heisencache\Routing
 */
class RouteProvider extends RouteSubscriberBase {
  const REPORT = 'heisencache.report';

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $route = (new Route('/admin/reports/heisencache'))->addDefaults([
      '_controller' => ReportController::class . '::build',
      '_title' => 'Heisencache report',
    ])->setRequirement('_permission', 'access site reports');
    $collection->add(self::REPORT, $route);
  }

}

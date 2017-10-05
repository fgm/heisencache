<?php

namespace Drupal\heisencache;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\heisencache\Cache\CacheInstrumentationPass;

/**
 * Class HeisencacheServiceProvider defines the module services.
 *
 * @package Drupal\heisencache
 */
class HeisencacheServiceProvider implements ServiceProviderInterface {

  /**
   * {@inheritdoc}
   *
   * Add a pass decorating cache services (bins, backends) with Heisencache.
   */
  public function register(ContainerBuilder $container) {
    $container->addCompilerPass(new CacheInstrumentationPass());
  }

}

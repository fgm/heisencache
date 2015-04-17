<?php
/**
 * @file
 * CacheFactory.php
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2015 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace Drupal\heisencache;


use Doctrine\Common\Util\Debug;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

class HeisencacheServiceProvider implements ServiceModifierInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $service_name = 'cache_factory';
    $hc_service_name = 'heisencache.cache_factory';
    $core_service_name = 'heisencache.core_cache_factory';

    // Rename core factory to hide it for normal use.
    $core_definition = $container->getDefinition($service_name);
    $container->removeDefinition($service_name);
    $container->addDefinitions([$core_service_name => $core_definition]);

    // Replace it with the HC factory:

    // 1. get the HC factory original definition
    $hc_definition = $container->getDefinition($hc_service_name);
    // 2. Add the newly appeared hidden core factory to its arguments.
    $hc_definition->addArgument(new Reference($core_service_name));
    // 3. Alias it to the original factory service name.
    $container->addAliases([$service_name => $hc_service_name]);
  }
}

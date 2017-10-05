<?php

namespace Drupal\heisencache\Cache;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class DecorateCachePass decorates cache bins and used cache backends.
 *
 * @package Drupal\heisencache\Cache
 *
 * @see \Drupal\heisencache\HeisencacheServiceProvider::register()
 */
class DecorateCachePass implements CompilerPassInterface {

  /**
   * {@inheritdoc}
   */
  public function process(ContainerBuilder $container) {
    $bins = $container->getParameter('cache_bins');
    array_walk($bins, [$this, 'decorateBin'], $container);
  }

  protected function decorateBin(string $bin, string $serviceId, ContainerBuilder $container) {
    $decoratorName = 'heisencache.decorating_' . $serviceId;
    $decoratedName = "{$decoratorName}.inner";

    $container->register($decoratorName, InstrumentedBin::class)
      ->setDecoratedService($serviceId)
      ->addArgument(new Reference($decoratedName))
      ->addArgument($bin)
      ->setPublic(TRUE);
  }

}

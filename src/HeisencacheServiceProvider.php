<?php

namespace Drupal\heisencache;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\heisencache\Cache\CacheInstrumentationPass;
use Drupal\heisencache\Menu\LinksProvider;
use Drupal\heisencache\Routing\RouteProvider;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Finder\Finder;

/**
 * Class HeisencacheServiceProvider defines the module services.
 *
 * @package Drupal\heisencache
 */
class HeisencacheServiceProvider implements ServiceProviderInterface {
  const MODULE = 'heisencache';
  const NS = 'EventSubscriber';
  const FQNS = __NAMESPACE__ . '\\' . self::NS;

  // Generic service names.
  const LINKS_PROVIDER = self::MODULE . '.links_provider';
  const ROUTE_PROVIDER = self::MODULE . '.route_provider';

  /**
   * @param \Drupal\Core\DependencyInjection\ContainerBuilder $container
   *   The container builder.
   */
  protected function discoverSubscribers(ContainerBuilder $container) {
    $subscribers = [];
    $finder = new Finder();
    $finder->files()->in(__DIR__ . '/' . self::NS);
    foreach ($finder as $file) {
      $name = basename($file->getRelativePathname(), '.php');
      $reflectionClass = new \ReflectionClass(self::FQNS . "\\$name");
      if (!$reflectionClass->isInstantiable()) {
        continue;
      }
      $serviceName = self::MODULE . '.subscriber.' . Container::underscore($name);
      echo "$serviceName\t";
    }
  }

  /**
   * Register the generic providers.
   *
   * @param \Drupal\Core\DependencyInjection\ContainerBuilder $container
   *   The container builder.
   */
  protected function registerGenericProviders(ContainerBuilder $container) {
    $container->register(self::ROUTE_PROVIDER, RouteProvider::class)
      ->addTag('event_subscriber');
    $container->register(self::LINKS_PROVIDER, LinksProvider::class);
  }

  /**
   * {@inheritdoc}
   *
   * Add a pass decorating cache services (bins, backends) with Heisencache.
   */
  public function register(ContainerBuilder $container) {
    $container->addCompilerPass(new CacheInstrumentationPass());
    $this->registerGenericProviders($container);
    $this->discoverSubscribers($container);
  }

}

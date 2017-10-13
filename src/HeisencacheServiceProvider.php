<?php

namespace Drupal\heisencache;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\heisencache\Cache\CacheInstrumentationPass;
use Drupal\heisencache\EventSubscriber\ConfigurableSubscriberInterface;
use Drupal\heisencache\Menu\LinksProvider;
use Drupal\heisencache\Routing\RouteProvider;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Finder\Finder;

/**
 * Class HeisencacheServiceProvider defines the module services.
 *
 * @package Drupal\heisencache
 */
class HeisencacheServiceProvider implements ServiceProviderInterface, ServiceModifierInterface {
  const MODULE = 'heisencache';
  const NS = 'EventSubscriber';
  const FQNS = __NAMESPACE__ . '\\' . self::NS;

  // Generic service names.
  const LINKS_PROVIDER = self::MODULE . '.links_provider';
  const ROUTE_PROVIDER = self::MODULE . '.route_provider';

  /**
   * @param \Drupal\Core\DependencyInjection\ContainerBuilder $container
   *
   * @return string
   */
  protected function getSubscriberConfiguration(ContainerBuilder $container): array {
    // Cannot access configuration during a container build, so use a parameter.
    $configuredServices = $container->getParameter('heisencache')['subscribers'];
    $result = [];
    foreach ($configuredServices as $baseName => $config) {
      $name = self::MODULE . ".subscriber.${baseName}";
      $result[$name] = $config;
    }

    return $result;
  }

  /**
   * Discover builtin subscribers.
   *
   * @return array<string,\ReflectionClass>
   *   An array names of configurable subscriber services.
   */
  protected function registerSubscribers(ContainerBuilder $container): array {
    $subscriberConfiguration = $this->getSubscriberConfiguration($container);

    $subscribers = [];
    $finder = new Finder();
    $finder->files()->in(__DIR__ . '/' . self::NS);
    foreach ($finder as $file) {
      $name = basename($file->getRelativePathname(), '.php');
      $reflectionClass = new \ReflectionClass(self::FQNS . "\\$name");

      // Only list actual configurable services.
      if (!$reflectionClass->isInstantiable()
      || !$reflectionClass->implementsInterface(ConfigurableSubscriberInterface::class)) {
        continue;
      }

      $serviceName = self::MODULE . '.subscriber.' . Container::underscore($name);
      $serviceName = preg_replace('/_subscriber$/', '', $serviceName);
      $subscribers[] = $serviceName;
    }

    return $subscribers;
  }

  /**
   * Register the generic providers.
   *
   * @param \Drupal\Core\DependencyInjection\ContainerBuilder $container
   *   The container builder.
   */
  protected function registerGenericProviders(ContainerBuilder $container) {
    $container->setParameter(self::MODULE, [
      'subscribers' => [
        'debug' => NULL,
      ],
    ]);
    $container->register(self::ROUTE_PROVIDER, RouteProvider::class)
      ->addTag('event_subscriber');
    $container->register(self::LINKS_PROVIDER, LinksProvider::class);
  }

  /**
   * {@inheritdoc}
   *
   * - Add a pass decorating cache services (bins, backends) with Heisencache.
   * - Register link and route provider services
   * - Register subscriber services
   */
  public function register(ContainerBuilder $container) {
    $container->addCompilerPass(new CacheInstrumentationPass());
    $this->registerGenericProviders($container);
  }

  public function alter(ContainerBuilder $container) {
    $this->registerSubscribers($container);
  }

}

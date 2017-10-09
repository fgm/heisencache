<?php

namespace Drupal\heisencache;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\heisencache\Cache\CacheInstrumentationPass;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Finder\Finder;

/**
 * Class HeisencacheServiceProvider defines the module services.
 *
 * @package Drupal\heisencache
 */
class HeisencacheServiceProvider implements ServiceProviderInterface {
  const NS = 'EventSubscriber';
  const FQNS = __NAMESPACE__ . '\\' . self::NS;

  protected function discoverSubscribers() {
    $subscribers = [];
    $finder = new Finder();
    $finder->files()->in(__DIR__ . '/' . self::NS);
    foreach ($finder as $file) {
      $name = basename($file->getRelativePathname(), '.php');
      $reflectionClass = new \ReflectionClass(self::FQNS . "\\$name");
      if (!$reflectionClass->isInstantiable()) {
        continue;
      }
      $serviceName = 'heisencache.subscriber.' . Container::underscore($name);
      echo "$serviceName\t";
    }
  }

  /**
   * {@inheritdoc}
   *
   * Add a pass decorating cache services (bins, backends) with Heisencache.
   */
  public function register(ContainerBuilder $container) {
    $container->addCompilerPass(new CacheInstrumentationPass());
    $this->discoverSubscribers();
  }

}

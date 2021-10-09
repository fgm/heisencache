<?php

namespace Drupal\heisencache;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\heisencache\Cache\CacheInstrumentationPass;
use Symfony\Component\Finder\Finder;

/**
 * Class HeisencacheServiceProvider defines the module services.
 *
 * @package Drupal\heisencache
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
class HeisencacheServiceProvider implements ServiceProviderInterface {
  const NS = __NAMESPACE__ . '\\EventSubscriber';

  protected function discoverSubscribers() {
    $finder = new Finder();
    $finder->files()->in(__DIR__ . '/EventSubscriber');
    foreach ($finder as $file) {
      $name = basename($file->getRelativePathname(), '.php');
      echo "$name: ";
      $reflectionClass = new \ReflectionClass(self::NS . "\\$name");
      echo ($reflectionClass->isInterface() ? 'Interface' : '') . " ";
      echo ($reflectionClass->isAbstract() ? 'Abstract' : '') . " ";
      echo ($reflectionClass->isTrait() ? 'Trait' : '') . " ";
      echo ($reflectionClass->isInstantiable() ? 'Instantiable' : '') . " ";
      echo "\n";
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

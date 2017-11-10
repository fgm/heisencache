<?php

namespace Drupal\heisencache;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\heisencache\Cache\CacheInstrumentationPass;
use Drupal\heisencache\Cache\CacheSubscriptionPass;
use Drupal\heisencache\EventSubscriber\ConfigurableListenerInterface;
use Drupal\heisencache\EventSubscriber\TerminateWriterInterface;
use Drupal\heisencache\Exception\ConfigurationException;
use Drupal\heisencache\Menu\LinksProvider;
use Drupal\heisencache\Routing\RouteProvider;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelEvents;

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
  const LOGGER = 'logger.channel.' . self::MODULE;

  const HELP_PROVIDER = self::MODULE . '.help_provider';
  const LINKS_PROVIDER = self::MODULE . '.links_provider';
  const ROUTE_PROVIDER = self::MODULE . '.route_provider';

  /**
   * Obtain the listener/subscriber configuration from the container parameters.
   *
   * @param \Drupal\Core\DependencyInjection\ContainerBuilder $container
   *   The container.
   *
   * @return array
   *   The parsed configuration.
   */
  protected function getSubscriberConfiguration(ContainerBuilder $container) : array {
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
  protected function discoverSubscribers(ContainerBuilder $container) : array {
    $subscriberConfiguration = $this->getSubscriberConfiguration($container);

    $configuredSubscribers = [];
    $finder = new Finder();
    $finder->files()->in(__DIR__ . '/' . self::NS);
    foreach ($finder as $file) {
      $name = basename($file->getRelativePathname(), '.php');
      $reflectionClass = new \ReflectionClass(self::FQNS . "\\$name");

      // Only list actual configurable services.
      if (!$reflectionClass->isInstantiable()
        || !$reflectionClass->implementsInterface(ConfigurableListenerInterface::class)) {
        continue;
      }

      $serviceName = self::MODULE . '.subscriber.' . Container::underscore($name);
      $serviceName = preg_replace('/_subscriber$/', '', $serviceName);
      // The NULL value has a specific meaning, so do not use isset/empty.
      if (array_key_exists($serviceName, $subscriberConfiguration)) {
        $configuredSubscribers[$serviceName] = [
          'events' => $subscriberConfiguration[$serviceName],
          'rc' => $reflectionClass,
        ];
        unset($subscriberConfiguration[$serviceName]);
      }
    }
    if (!empty($subscriberConfiguration)) {
      throw new ConfigurationException(strtr('Configuration requests non-discovered subscribers: @subscribers.', [
        '@subscribers' => implode(', ', array_keys($subscriberConfiguration)),
      ]));
    }

    return $configuredSubscribers;
  }

  /**
   * Register the generic providers.
   *
   * @param \Drupal\Core\DependencyInjection\ContainerBuilder $container
   *   The container builder.
   */
  protected function registerGenericProviders(ContainerBuilder $container) {
    $definition = (new DefinitionDecorator('logger.channel_base'))
      ->addArgument(self::MODULE);
    $container->setDefinition(self::LOGGER, $definition);

    $container->register(self::HELP_PROVIDER, HelpProvider::class);
    $container->register(self::ROUTE_PROVIDER, RouteProvider::class)
      ->addTag('event_subscriber');
    $container->register(self::LINKS_PROVIDER, LinksProvider::class);
  }

  /**
   * Register the default Heisencache parameter configuration.
   *
   * @param \Drupal\Core\DependencyInjection\ContainerBuilder $container
   *   The container in which to register parameters.
   */
  protected function registerParameters(ContainerBuilder $container) {
    $container->setParameter(self::MODULE, [
      'subscribers' => [],
    ]);
  }

  /**
   * Register a configured listeners in the container.
   *
   * @param \Drupal\Core\DependencyInjection\ContainerBuilder $container
   *   The container in which to register this listener service.
   * @param string $name
   *   The name under which to register the service.
   * @param array|null $events
   *   The events which the service listens to.
   * @param \ReflectionClass $rc
   *   The reflection class for the service.
   */
  protected function registerSubscriber(ContainerBuilder $container, string $name, $events, \ReflectionClass $rc) {
    $definition = call_user_func([$rc->getName(), 'describe']);
    foreach ((array) $events as $eventName) {
      $definition->addMethodCall('addEvent', [$eventName]);
    }
    if (empty($events) && in_array(TerminateWriterInterface::class, $rc->getInterfaceNames())) {
      $definition->addMethodCall('addEvent', [KernelEvents::TERMINATE, TRUE]);
    }
    $container->setDefinition($name, $definition);
  }

  /**
   * {@inheritdoc}
   *
   * - Add a pass decorating cache services (bins, backends) with Heisencache.
   * - Register link and route provider services.
   * - Do NOT register subscriber services: the parameter configuring them is
   *   not yet available at this step.
   *
   * @see \Drupal\heisencache\HeisencacheServiceProvider::alter()
   */
  public function register(ContainerBuilder $container) {
    // Add decoractor services before optimization.
    $container->addCompilerPass(new CacheInstrumentationPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION);
    // But modify the event_dispatcher subscriptions after they have been setup
    // during RegisterEventSubscriberPass, which runs after removing.
    $container->addCompilerPass(new CacheSubscriptionPass(), PassConfig::TYPE_AFTER_REMOVING);

    $this->registerGenericProviders($container);
    $this->registerParameters($container);
  }

  /**
   * {@inheritdoc}
   *
   * Heisencache subscribers need to be registered during the container alter
   * phase since they are dynamic: the parameters are not yet available during
   * the register() phase: only the default configuration is available at this
   * point.
   */
  public function alter(ContainerBuilder $container) {
    $subscribers = $this->discoverSubscribers($container);
    foreach ($subscribers as $name => $info) {
      $this->registerSubscriber($container, $name, $info['events'], $info['rc']);
    }
  }

}

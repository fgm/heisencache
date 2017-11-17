<?php

namespace Drupal\heisencache\Cache;

use Drupal\heisencache\Event\EventBase;
use Drupal\heisencache\EventSubscriber\ConfigurableListenerInterface;
use Drupal\heisencache\Exception\ConfigurationException;
use Drupal\heisencache\HeisencacheServiceProvider as H;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class CacheSubscriptionPass registers the Heisencache event listeners.
 *
 * It is needed because their configuration is parametric at runtime, unlike
 * the standard event subscribers, which may not change their subscriptions per-
 * instance.
 *
 * @package Drupal\heisencache\Cache
 */
class CacheSubscriptionPass implements CompilerPassInterface {

  /**
   * The listeners array is argument 1 (not 0) on the event dispatcher service.
   * Although a cursory reading of core.services.yml could let one think it only
   * takes 1 argument, it actually takes 2, the second one being added by a
   * compiler pass running before this one.
   */
  const DISPATCHER_LISTENERS_ARG = 1;

  const LISTENER_CLASS = ConfigurableListenerInterface::class;

  /**
   * Add listeners for a service to the global listeners list.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
   *   The freshly built container.
   * @param string $id
   *   The service name.
   * @param array $listeners
   *   The initial listeners list.
   *
   * @return array
   *   The updated listeners list.
   */
  protected function addServiceListeners(ContainerBuilder $container, string $id, array $listeners): array {
    // We must assume that the class value has been correctly filled, even if
    // the service is created by a factory.
    $listenerClass = $container->getDefinition($id)->getClass();

    // Only process classes with the listener interface.
    $refClass = new \ReflectionClass($listenerClass);
    if (!$refClass->implementsInterface(self::LISTENER_CLASS)) {
      throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, self::LISTENER_CLASS));
    }

    $listenerMethods = array_reduce($refClass->getMethods(), [$this, 'reduceMethod'], []);
    unset($refClass);

    // At this point in the compilation, the service can be instantiated.
    /** @var \Drupal\heisencache\EventSubscriber\ConfigurableListenerInterface $listener */
    $listener = $container->get($id);
    // Get all listened events. The format is plainer than Symfony's.
    foreach ($listener->getListenedEvents() as $short_event_name => $event_name) {
      if (preg_match('/^(after|before|in)_/', $event_name)) {
        $event_name = H::MODULE . ".${short_event_name}";
      }
      $priority = 0;
      $callback = EventBase::callbackFromEventName($event_name);

      if (!method_exists($listener, $callback)) {
        if (!method_exists($listener, 'isListenedTo') || !$listener->isListenedTo($short_event_name)) {
          throw new ConfigurationException("Listener $id has no method to listen to event $event_name");
        }
      }

      $listeners[$event_name][$priority][] = [
        'service' => [$id, $callback],
      ];
    }

    return $listeners;
  }

  /**
   * {@inheritdoc}
   *
   * Update the listeners known to the event_dispatcher by adding the
   * dynamically configured Heisencache listeners.
   *
   * @see \Drupal\Core\DependencyInjection\Compiler\RegisterEventSubscribersPass::process()
   */
  public function process(ContainerBuilder $container) {
    if (!$container->hasDefinition(H::DISPATCHER)) {
      return;
    }
    $dispatcherDefinition = $container->getDefinition(H::DISPATCHER);

    // Find the initial listeners.
    $listeners = $dispatcherDefinition->getArgument(self::DISPATCHER_LISTENERS_ARG);

    // Add listeners provided by configured Heisencache services.
    foreach (array_keys($container->findTaggedServiceIds(ConfigurableListenerInterface::LISTENER_TAG)) as $id) {
      $listeners = $this->addServiceListeners($container, $id, $listeners);
    }

    // Sort listeners for each event.
    foreach (array_keys($listeners) as $event_name) {
      krsort($listeners[$event_name]);
    }

    // Update the event_dispatcher with the completed listeners array.
    $dispatcherDefinition->replaceArgument(self::DISPATCHER_LISTENERS_ARG, $listeners);
  }

  /**
   * @return \Closure
   */
  protected function reduceMethod(array $carry, \ReflectionMethod $method) {
    $name = $method->getName();
    if (preg_match('/^(after|before|in)[A-Z].+$/', $name)) {
      $carry[] = $name;
    }
    return $carry;
  }

}

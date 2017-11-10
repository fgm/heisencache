<?php

namespace Drupal\heisencache\Cache;

use Drupal\heisencache\Event\EventBase;
use Drupal\heisencache\EventSubscriber\ConfigurableListenerInterface;
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
   * {@inheritdoc}
   *
   * @see \Drupal\Core\DependencyInjection\Compiler\RegisterEventSubscribersPass::process()
   */
  public function process(ContainerBuilder $container) {
    if (!$container->hasDefinition('event_dispatcher')) {
      return;
    }

    $definition = $container->getDefinition('event_dispatcher');
    $argIndex = 1;
    $subscribers = $definition->getArgument($argIndex);
    $interface = ConfigurableListenerInterface::class;

    foreach (array_keys($container->findTaggedServiceIds(ConfigurableListenerInterface::LISTENER_TAG)) as $id) {

      // We must assume that the class value has been correctly filled, even if
      // the service is created by a factory.
      $listenerClass = $container->getDefinition($id)->getClass();

      $refClass = new \ReflectionClass($listenerClass);
      if (!$refClass->implementsInterface($interface)) {
        throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, $interface));
      }
      $listenerMethods = array_reduce($refClass->getMethods(), function (array $carry, \ReflectionMethod $method) {
        $name = $method->getName();
        if (preg_match('/^(after|before|on)[A-Z].+$/', $name)) {
          $carry[] = $name;
        }
        return $carry;
      }, []);
      unset($refClass);

      // At this point in the compilation, the service can be instantiated.
      /** @var \Drupal\heisencache\EventSubscriber\ConfigurableListenerInterface $listener */
      $listener = $container->get($id);
      // Get all subscribed events. The format is plainer than Symfony's.
      foreach ($listener->getSubscribedEvents() as $short_event_name => $event_name) {
        $event_name = ($event_name === $short_event_name)
          ? H::MODULE . ".${short_event_name}"
          : $short_event_name;
        $priority = 0;
        $candidateCallbacks = EventBase::callbacksFromEventName($event_name);
        $callbacks = array_intersect($candidateCallbacks, $listenerMethods);
        foreach ($callbacks as $callback) {
          $subscribers[$event_name][$priority][] = [
            'service' => [$id, $callback],
          ];
        }
      }
      unset($listener, $short_event_name, $event_name, $priority);
    }
    unset($id, $interface);

    foreach (array_keys($subscribers) as $event_name) {
      krsort($subscribers[$event_name]);
    }

    $definition->replaceArgument($argIndex, $subscribers);
  }

}

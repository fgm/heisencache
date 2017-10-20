<?php


namespace Drupal\heisencache\EventSubscriber;

use Drupal\heisencache\DescribedServiceInterface;
use Symfony\Component\DependencyInjection\Definition;

abstract class ConfigurableListenerBase implements ConfigurableListenerInterface, DescribedServiceInterface {

  /**
   * An array of the events subscribed by instances of this class.
   *
   * Note that each new instance may add new events, never remove any.
   *
   * @var array
   */
  public $subscribedEvents = [];

  /**
   * ConfigurableSubscriberBase constructor.
   *
   * @param array $events
   *   An array of event names to add to the list of static subscribed events.
   */
  public function __construct(array $events = []) {
    array_walk($events, [$this, 'addEvent']);
  }

  /**
   * {@inheritdoc}
   */
  public function addEvent($eventName): void {
    $this->subscribedEvents[$eventName] = $eventName;
  }

  /**
   * {@inheritdoc}
   */
  public static function describe(): Definition {
    $def = new Definition(get_called_class());
    $def->addArgument([])
      ->addTag(ConfigurableListenerInterface::TAG)
      ->setPublic(TRUE);
    return $def;
  }

  /**
   * {@inheritdoc}
   */
  public function getSubscribedEvents(): array {
    return array_keys($this->subscribedEvents);
  }

  /**
   * {@inheritdoc}
   */
  public function removeEvent($eventName): void {
    unset($this->subscribedEvents[$eventName]);
  }

}

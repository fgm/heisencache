<?php


namespace Drupal\heisencache\EventSubscriber;

use Drupal\heisencache\DescribedServiceInterface;
use Symfony\Component\DependencyInjection\Definition;

abstract class ConfigurableListenerBase implements ConfigurableListenerInterface, DescribedServiceInterface {

  /**
   * An array of the events instances of this class listened to.
   *
   * Note that each new instance may add new events, never remove any.
   *
   * @var array
   */
  public $listenedEvents = [];

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
  public function addEvent(string $eventName): void {
    $this->listenedEvents[$eventName] = $eventName;
  }

  /**
   * {@inheritdoc}
   */
  public static function describe(array $knownEvents = []): Definition {
    $def = new Definition(get_called_class());
    $def->addArgument([])
      ->addTag(ConfigurableListenerInterface::LISTENER_TAG)
      ->setPublic(TRUE);
    return $def;
  }

  /**
   * {@inheritdoc}
   */
  public function getListenedEvents(): array {
    return $this->listenedEvents;
  }

  /**
   * {@inheritdoc}
   */
  public function removeEvent($eventName): void {
    unset($this->listenedEvents[$eventName]);
  }

}

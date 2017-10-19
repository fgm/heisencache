<?php


namespace Drupal\heisencache\EventSubscriber;

use Drupal\heisencache\DescribedServiceInterface;
use Symfony\Component\DependencyInjection\Definition;

abstract class ConfigurableSubscriberBase implements ConfigurableSubscriberInterface, DescribedServiceInterface {

  /**
   * An array of the events subscribed by instances of this class.
   *
   * Note that each new instance may add new events, never remove any.
   *
   * @var array
   */
  public static $subscribedEvents = [];

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
  public static function addEvent($eventName) {
    static::$subscribedEvents[$eventName] = $eventName;
  }

  /**
   * {@inheritdoc}
   */
  public static function describe(): Definition {
    $def = new Definition(get_called_class());
    $def->addArgument([])
      ->addTag('event_subscriber')
      ->setPublic(TRUE);
    return $def;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return array_keys(static::$subscribedEvents);
  }

  /**
   * {@inheritdoc}
   */
  public static function removeEvent($eventName) {
    unset(static::$subscribedEvents[$eventName]);
  }

}

<?php

namespace Drupal\heisencache;

/**
 * EventEmitter is inspired by NodeJS EventEmitter.
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
class EventEmitter {

  /**
   * The subscribers.
   *
   * @var array[string][Drupal\heisencache\EventSubscriberInterface]
   */
  protected array $subscribers;

  /**
   * Bind a subscriber to an event name.
   *
   * @param string $eventName
   *   The name of the event on which to act.
   * @param \Drupal\heisencache\EventSubscriberInterface $subscriber
   *   The subscriber instance.
   *
   * @return int
   *   The number of subscribers for this event.
   *
   * @throws \InvalidArgumentException
   */
  public function on($eventName, EventSubscriberInterface $subscriber) {
    $hash = spl_object_hash($subscriber);
    $nameArg = [
      '@eventName' => $eventName,
    ];
    if (!isset($this->subscribers[$eventName][$hash])) {
      if (is_callable([$subscriber, $eventName])) {
        $this->subscribers[$eventName][$hash] = $subscriber;
      }
      else {
        throw new \InvalidArgumentException(
          strtr("Trying to subscribe to unsupported event @eventName", $nameArg)
        );
      }
    }
    else {
      if ($subscriber === $this->subscribers[$eventName][$hash]) {
        /* Nothing to do:
        - if the subscriber is the same, the hash has not been recycled and
        is already subscribed, so nothing to do;
        - else, because of the subscriber reference in $this, even an unset
        subscriber will still have a reference and the spl_object_hash will not
        be reused, so this case cannot happen without a PHP runtime bug.
         */
      }
    }

    return count($this->subscribers[$eventName]);
  }

  /**
   * Send an event to all subscribers.
   *
   * @param string $eventName
   *   The name of the event.
   * @param string $channel
   *   The channel on which to send the event.
   *
   * @return int
   *   The number of subscribers to which the event was sent.
   */
  public function emit($eventName, $channel) {
    if (empty($this->subscribers[$eventName])) {
      return 0;
    }
    $args = func_get_args();
    array_shift($args);

    foreach ($this->subscribers[$eventName] as $subscriber) {
      call_user_func_array([$subscriber, $eventName], $args);
    }
    return count($this->subscribers[$eventName]);
  }

  /**
   * Register an event subscriber with the event emitter for all its events..
   *
   * @param \Drupal\heisencache\EventSubscriberInterface $subscriber
   *   The subscriber instance to register.
   *
   * @return \Drupal\heisencache\EventEmitter
   *   The updated emitter.
   */
  public function register(EventSubscriberInterface $subscriber) {
    foreach ($subscriber->getSubscribedEvents() as $eventName) {
      $this->on($eventName, $subscriber);
    }
    return $this;
  }

  /**
   * Return the list of subscribers for a given event.
   *
   * @param string $eventName
   *   The name of the event.
   *
   * @return \Drupal\heisencache\EventSubscriberInterface[]
   *   The list of subscribers
   */
  public function getSubscribersByEventName($eventName) {
    return $this->subscribers[$eventName] ?? [];
  }

}

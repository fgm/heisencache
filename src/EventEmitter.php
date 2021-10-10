<?php
/**
 * @file
 *   EventEmitter.php
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

declare(strict_types=1);

namespace Drupal\heisencache;

use Drupal\heisencache\EventSubscriber\EventSubscriberInterface;

class EventEmitter {

  /**
   * @var array[string][Drupal\heisencache\EventSubscriber\EventSubscriberInterface]
   */
  protected array $subscribers;

  /**
   * Bind a subscriber to an event name.
   *
   * @param string $eventName
   * @param \Drupal\heisencache\EventSubscriber\EventSubscriberInterface $subscriber
   *
   * @return int
   * @throws \InvalidArgumentException
   */
  public function on(string $eventName, EventSubscriberInterface $subscriber): int {
    $hash = spl_object_hash($subscriber);
    $nameArg = ['@eventName' => $eventName];
    if (!isset($this->subscribers[$eventName][$hash])) {
      if (is_callable([$subscriber, $eventName])) {
        $this->subscribers[$eventName][$hash] = $subscriber;
      }
      else {
        throw new \InvalidArgumentException(
          strtr(
            "Trying to subscribe to unsupported event @eventName",
            $nameArg
          )
        );
      }
    }
    elseif ($subscriber === $this->subscribers[$eventName][$hash]) {
      /* Nothing to do:
         - if the subscriber is the same, the hash has not been recycled and
           is already subscribed, so nothing to do;
         - else, because of the subscriber reference in $this, even an unset
           subscriber will still have a reference and the spl_object_hash
           will not be reused, so this case cannot happen without a PHP
           runtime bug.
      */
    }

    return count($this->subscribers[$eventName]);
  }

  /**
   * Send an event to all subscribers.
   *
   * @param string $eventName
   *   The name of the event.
   *
   * @param string $channel
   *
   * @return int
   *   The number of subscribers to which the event was sent.
   */
  public function emit($eventName, $channel): int {
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
   * @param \Drupal\heisencache\EventSubscriber\EventSubscriberInterface $subscriber
   *
   * @return \Drupal\heisencache\EventEmitter
   */
  public function register(EventSubscriberInterface $subscriber): EventEmitter {
    foreach ($subscriber->getSubscribedEvents() as $eventName) {
      $this->on($eventName, $subscriber);
    }
    return $this;
  }

  /**
   * Return the list of subscribers for a given event.
   *
   * @param string $eventName
   *
   * @return \Drupal\heisencache\EventSubscriber\EventSubscriberInterface[]
   */
  public function getSubscribersByEventName($eventName) {
    return $this->subscribers[$eventName] ?? [];
  }

}

<?php
/**
 * @file
 *   EventEmitter.php
 *
 * @author: marand
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache;

class EventEmitter {
  /**
   * @var array[string][EventSubscriberInterface]
   */
  protected $subscribers;

  /**
   * Bind a subscriber to an event name.
   *
   * @param string $eventName
   * @param \OSInet\Heisencache\EventSubscriberInterface $subscriber
   *
   * @throws \InvalidArgumentException
   */
  public function on($eventName, EventSubscriberInterface $subscriber) {
    $hash = spl_object_hash($subscriber);
    $nameArg = array(
      '@eventName' => $eventName,
    );
    if (!isset($this->subscribers[$eventName][$hash])) {
      if (is_callable(array($subscriber, $eventName))) {
        $this->subscribers[$eventName][$hash] = $subscriber;
      }
      else {
        throw new \InvalidArgumentException(strtr("Trying to subscribe to unsupported event @eventName", $nameArg));
      }
    }
    else  {
      if ($subscriber === $this->subscribers[$eventName][$hash]) {
        /* Nothing to do:
           - if the subscriber is the same, the hash has not been recycled and
             is already subscribed, so nothing to do;
           - else, because of the subscriber reference in $this, even an unset
             subscriber will still have a reference and the spl_object_hash
             will not be reused, so this case cannot happen without a PHP
             runtime bug.
        */
      }
    }
  }

  /**
   * Send an event to all subscribers.
   *
   * @param string $eventName
   *   The name of the event.
   *
   * @return int
   *   The number of subscribers to which the event was sent.
   */
  public function emit($eventName) {
    if (empty($this->subscribers[$eventName])) {
      return 0;
    }
    $args = func_get_args();
    array_shift($args);

    foreach ($this->subscribers[$eventName] as $subscriber) {
      call_user_func_array(array($subscriber, $eventName), $args);
    }
    return count($this->subscribers[$eventName]);
  }

  /**
   * Register an event subscriber with the event emitter for all its events..
   *
   * @param EventSubscriberInterface $subscriber
   *
   * @return void
   */
  public function register(EventSubscriberInterface $subscriber) {
    foreach ($subscriber->getEvents() as $eventName) {
      $this->on($eventName, $subscriber);
    }
  }

  /**
   * Return the list of subscribers for a given event.
   *
   * @param string $eventName
   *
   * @return \OSInet\Heisencache\EventSubscriberInterface[]
   */
  public function getSubscribersByEventName($eventName) {
    $ret = isset($this->subscribers[$eventName])
      ? $this->subscribers[$eventName]
      : array();

    return $ret;
  }
}

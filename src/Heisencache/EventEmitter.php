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
    elseif ($subscriber === $this->subscribers[$eventName][$hash]) {
      // Nothing to do: the hash has not been recycled and is already subscribed.
    }
    else {
      throw new \InvalidArgumentException(strtr("Trying two register a new subscriber with an existing object hash on the same event (@eventName).", $nameArg));
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
   * Register an event subscriber with the event emitter.
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
}

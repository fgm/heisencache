<?php

namespace Drupal\heisencache\Event;

use Drupal\heisencache\HeisencacheServiceProvider as H;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class EventBase
 *
 * Unlike the base Symfony Event class, this one always contains a bin, which
 * may be empty on a few events only, like system shutdown. Events are marked
 * as "pre" by default on construction, and can be set to "post", by the
 * fluent setPost() method.
 *
 * @package Drupal\heisencache\Event
 */
abstract class EventBase extends Event implements EventInterface {
  /**
   * @var string
   *  "$name", although deprecated, still exists in Symfony Event class, so we
   *  may not overwrite it.
   */
  public $eventName = 'base';

  /**
   * @var string
   *   No need for getter/setter: no side effects.
   */
  public $bin;

  /**
   * @var mixed[]
   *   Whatever data a concrete event may want to store.
   */
  protected $data;

  /**
   * @var string
   *   EventInterface::PRE|POST
   */
  public $kind;

  /**
   * EventBase constructor.
   *
   * @param string $bin
   *   The name of the bin on which an event is being dispatched.
   * @param string $kind
   *   The kind of event: self::(PRE|IN\POST).
   * @param array $data
   *   Optional: event data.
   */
  public function __construct($bin, $kind = self::PRE, array $data = [])  {
    $data += ['id' => $_SERVER['UNIQUE_ID'] ?? 'unknown_id'];
    $this->bin = $bin;
    $this->kind = $kind;
    $this->data = $data;
    // __CLASS__ would give this class, unlike get_class($this).
    $event_name = substr(strrchr(get_class($this), '\\'), 1);
    $event_name = Container::underscore($event_name);
    $this->eventName = H::MODULE . ".${event_name}";
  }

  /**
   * {@inheritdoc}
   */
  public function kind() {
    return $this->kind;
  }

  /**
   * {@inheritdoc}
   */
  public function name() {
    return $this->eventName;
  }

  /**
   * Allow propagation (again) on event.
   *
   * Symfony Event does not allow this, hence the Reflection access override.
   */
  public function restartPropagation() {
    if ($this->isPropagationStopped()) {
      $rm = new \ReflectionProperty($this, 'propagationStopped');
      $rm->setAccessible(TRUE);
      $rm->setValue($this, FALSE);
    }

    return $this;
  }

  /**
   * @param mixed[] $data
   *
   * @return $this
   */
  public function setData(array $data) {
    array_merge($this->data, $data);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setPost() {
    $this->restartPropagation();
    $this->kind = static::POST;
    return $this;
  }
}

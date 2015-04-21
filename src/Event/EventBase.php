<?php
/**
 * @file
 * EventBase.php
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2015 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace Drupal\heisencache\Event;


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

  public function __construct($bin, $kind = self::PRE, array $data = [])  {
    $this->bin = $bin;
    $this->kind = $kind;
    $this->data = $data;
    $class = explode('\\', get_class($this));
    $event_name = array_pop($class);
    $event_name = strtolower(preg_replace('/[A-Z]*/g', '_$1', $event_name));
    $this->eventName = $event_name;
  }

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

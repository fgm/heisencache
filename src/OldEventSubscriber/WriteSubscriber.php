<?php

namespace Drupal\heisencache\EventSubscriber;

/**
 * Class WriteSubscriber listens to calls mutating the cache bin contents.
 *
 * @package Drupal\heisencache\EventSubscriber
 */
class WriteSubscriber extends ConfigurableListenerBase implements EventSourceInterface {

  use EventSourceTrait;

  const NAME = "writes";

  protected $subscribedEvents = [
    'afterSet' =>  1,
    'afterClear' => 1,
  ];

  protected static $emittedEvents = [
    'write',
  ];

  /**
   * Event handler for afterSet.
   *
   * @param string $channel
   * @param string $cid
   * @param mixed $value
   * @param int $expire
   *
   * @return array
   */
  public function afterSet($channel, $cid, $value, $expire) {
    $writeInfo = array(
      'subscriber' => static::NAME,
      'op' => 'set',
      'bin' => $channel,
      'requested' => array($cid),
      'value_size' => strlen(serialize($value)),
      'expire' => $expire,
    );

    $this->emitter->emit('write', $channel, $writeInfo);

    return $writeInfo;
  }

  /**
   * Event handler for afterClear.
   *
   * @param string $channel
   * @param string $cid
   * @param boolean $wildcard
   *
   * @return array
   */
  public function afterClear($channel, $cid, $wildcard) {
    $clearInfo = array(
      'subscriber' => static::NAME,
      'op' => 'clear',
      'bin' => $channel,
      'requested' => array($cid),
      'wildcard' => $wildcard ? 1 : 0,
    );

    $this->emitter->emit('write', $channel, $clearInfo);

    return $clearInfo;
  }
}

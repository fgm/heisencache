<?php
/**
 * @file
 * A subscriber on cache set and clear events.
 *
 * @author: bpresles
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache;


class WriteSubscriber extends EventSourceSubscriber {

  const NAME = "writes";

  protected $subscribedEvents = array(
    'afterSet' =>  1,
    'afterClear' => 1,
  );

  protected static $emittedEvents = array(
    'write',
  );

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

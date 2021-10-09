<?php

declare(strict_types=1);

namespace Drupal\heisencache\EventSubscriber;

/**
 * A subscriber on cache set and clear events.
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
class WriteSubscriber extends EventSourceSubscriber {

  const NAME = "writes";

  protected $subscribedEvents = [
    'afterSet' => 1,
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
  public function afterSet(string $channel, string $cid, $value, int $expire): array {
    $writeInfo = [
      'subscriber' => static::NAME,
      'op' => 'set',
      'bin' => $channel,
      'requested' => [$cid],
      'value_size' => strlen(serialize($value)),
      'expire' => $expire,
    ];

    $this->emitter->emit('write', $channel, $writeInfo);

    return $writeInfo;
  }

  /**
   * Event handler for afterClear.
   *
   * @param string $channel
   * @param string $cid
   * @param bool $wildcard
   *
   * @return array
   */
  public function afterClear(string $channel, string $cid, bool $wildcard): array {
    $clearInfo = [
      'subscriber' => static::NAME,
      'op' => 'clear',
      'bin' => $channel,
      'requested' => [$cid],
      'wildcard' => $wildcard ? 1 : 0,
    ];

    $this->emitter->emit('write', $channel, $clearInfo);

    return $clearInfo;
  }

}

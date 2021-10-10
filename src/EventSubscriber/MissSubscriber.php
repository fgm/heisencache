<?php

declare(strict_types=1);

namespace Drupal\heisencache\EventSubscriber;

/**
 * A subscriber on cache miss events.
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
class MissSubscriber extends EventSourceSubscriber {

  const NAME = "misses";

  protected array $subscribedEvents = [
    'afterGet' => 1,
    'afterGetMultiple' => 1,
    'beforeGetMultiple' => 1,
  ];

  protected static array $emittedEvents = [
    'miss',
    'missMultiple',
  ];

  protected $multipleCids = [];

  /**
   * @param string $channel
   * @param string $cid
   * @param mixed $value
   *
   * @return array
   */
  public function afterGet($channel, $cid, $value): array {
    if ($value === FALSE) {
      $missInfo = [
        'subscriber' => static::NAME,
        'op' => 'get',
        'bin' => $channel,
        'requested' => [$cid],
        'misses' => [$cid],
      ];

      $this->emitter->emit('miss', $channel, $missInfo);
    }
    else {
      $missInfo = [];
    }

    return $missInfo;
  }

  /**
   * @param string $channel
   * @param string[] $missed_cids
   *
   * @return array
   */
  public function afterGetMultiple(string $channel, array $missed_cids): array {
    $requested = $this->multipleCids;
    $this->multipleCids = [];

    if (!empty($missed_cids)) {
      $missInfo = [
        'subscriber' => static::NAME,
        'op' => 'get_multiple',
        'bin' => $channel,
        'requested' => $requested,
        'misses' => $missed_cids,
      ];
      $missInfo['full_miss'] = ($missed_cids == $requested);

      $this->emitter->emit('missMultiple', $channel, $missInfo);
    }
    else {
      $missInfo = [];
    }

    return $missInfo;
  }

  public function beforeGetMultiple(string $_channel, array $cids): void {
    $this->multipleCids = $cids;
  }

}

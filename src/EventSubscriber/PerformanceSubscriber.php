<?php

declare(strict_types=1);

namespace Drupal\heisencache\EventSubscriber;

/**
 * A subscriber on cache performance events.
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
class PerformanceSubscriber extends EventSourceSubscriber {

  const NAME = "performance";

  protected array $subscribedEvents = [
    'beforeClear' => 1,
    'afterClear' => 1,

    'beforeIsEmpty' => 1,
    'afterIsEmpty' => 1,

    'beforeSet' => 1,
    'afterSet' => 1,

    'beforeGet' => 1,
    'afterGet' => 1,

    'beforeGetMultiple' => 1,
    'afterGetMultiple' => 1,
  ];

  protected static array $emittedEvents = ['performance'];

  protected static array $timers = [];

  protected array $pendingGetMultiple;

  public static function getTimerId(string $channel, array $cacheId): string {
    array_unshift($cacheId, $channel);
    return serialize($cacheId);
  }

  /**
   * @param string $channel
   */
  public function beforeClear(string $channel): void {
    $timer_id = static::getTimerId($channel, []);
    static::$timers[$timer_id] = microtime(TRUE);
  }

  /**
   * @param string $channel
   *
   * @return array
   */
  public function afterClear(string $channel, ?string $cid, bool $wildcard): array {
    $timerId = static::getTimerId($channel, []);
    $performanceInfo = [
      'subscriber' => static::NAME,
      'op' => 'clear',
      'bin' => $channel,
      'delay' => (microtime(TRUE) - static::$timers[$timerId]) * 1E3,
    ];
    unset(static::$timers[$timerId]);

    $this->emitter->emit('performance', $channel, $performanceInfo);

    return $performanceInfo;
  }

  /**
   * @param string $channel
   * @param string $cid
   */
  public function beforeGet(string $channel, string $cid): void {
    $timer_id = static::getTimerId($channel, [$cid]);
    static::$timers[$timer_id] = microtime(TRUE);
  }

  /**
   * @param string $channel
   * @param string $cid
   * @param mixed $value
   *
   * @return array
   */
  public function afterGet(string $channel, string $cid, $value): array {
    $timer_id = static::getTimerId($channel, [$cid]);
    $performanceInfo = [
      'subscriber' => static::NAME,
      'op' => 'get',
      'bin' => $channel,
      'requested' => [$cid],
      'delay' => (microtime(TRUE) - static::$timers[$timer_id]) * 1E3,
    ];
    unset(static::$timers[$timer_id]);

    // Most back-ends will use at least 1 32-bit word to return FALSE.
    $missInfo['size'] = ($value === FALSE) ? 4 : strlen(serialize($value->data));

    $this->emitter->emit('performance', $channel, $performanceInfo);

    return $performanceInfo;
  }

  /**
   * @param string $channel
   * @param array $cids
   */
  public function beforeGetMultiple(string $channel, array $cids): void {
    $timerId = static::getTimerId($channel, $cids);
    $this->pendingGetMultiple[$channel] = $cids;
    static::$timers[$timerId] = microtime(TRUE);
  }

  /**
   * @param string $channel
   * @param string[] $missed_cache_ids
   * @param array $result
   *
   * @return array
   */
  public function afterGetMultiple(string $channel, array $missed_cache_ids, array $result): array {
    $cacheIds = $this->pendingGetMultiple[$channel];
    $timerId = static::getTimerId($channel, $cacheIds);
    $performanceInfo = [
      'subscriber' => static::NAME,
      'op' => 'getMultiple',
      'bin' => $channel,
      'requested' => $cacheIds,
      'delay' => (microtime(TRUE) - static::$timers[$timerId]) * 1E3,
    ];
    unset(static::$timers[$timerId]);

    // Most back-ends will use at least 1 32-bit word to return FALSE.
    $size = 4 * count($missed_cache_ids);

    foreach ($result as $data) {
      $size += strlen(serialize($data));
    }
    $performanceInfo['size'] = $size;

    $this->emitter->emit('performance', $channel, $performanceInfo);

    return $performanceInfo;
  }

  /**
   * @param string $channel
   */
  public function beforeIsEmpty(string $channel): void {
    $timerId = static::getTimerId($channel, []);
    static::$timers[$timerId] = microtime(TRUE);
  }

  /**
   * @param string $channel
   *
   * @return array
   */
  public function afterIsEmpty(string $channel): array {
    $timerId = static::getTimerId($channel, []);
    $performanceInfo = [
      'subscriber' => static::NAME,
      'op' => 'isEmpty',
      'bin' => $channel,
      'delay' => (microtime(TRUE) - static::$timers[$timerId]) * 1E3,
    ];
    unset(static::$timers[$timerId]);

    $this->emitter->emit('performance', $channel, $performanceInfo);

    return $performanceInfo;
  }

  /**
   * @param string $channel
   * @param string $cid
   */
  public function beforeSet(string $channel, string $cid): void {
    $timerId = static::getTimerId($channel, [$cid]);
    static::$timers[$timerId] = microtime(TRUE);
  }

  /**
   * @param string $channel
   * @param string $cid
   * @param mixed $data
   *
   * @return array
   */
  public function afterSet(string $channel, string $cid, $data): array {
    $timerId = static::getTimerId($channel, [$cid]);
    $performanceInfo = [
      'subscriber' => static::NAME,
      'op' => 'set',
      'bin' => $channel,
      'requested' => [$cid],
      'delay' => (microtime(TRUE) - static::$timers[$timerId]) * 1E3,
      'size' => strlen(serialize($data)),
    ];
    unset(static::$timers[$timerId]);

    $this->emitter->emit('performance', $channel, $performanceInfo);

    return $performanceInfo;
  }

}

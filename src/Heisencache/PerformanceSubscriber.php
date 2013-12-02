<?php
/**
 * @file
 * A subscriber on cache performance events.
 *
 * @author: marand
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache;


class PerformanceSubscriber extends BaseEventSubscriber implements EventSourceInterface {

  const NAME = "performance";

  /**
   * @var \OSInet\Heisencache\EventEmitter
   */
  protected $emitter;

  protected $subscribedEvents = array(
    'beforeClear' => 1,
    'afterClear' => 1,

    'beforeIsEmpty' => 1,
    'afterIsEmpty' => 1,

    'beforeSet' => 1,
    'afterSet' => 1,

    'beforeGet' => 1,
    'afterGet' =>  1,

    'beforeGetMultiple' => 1,
    'afterGetMultiple' => 1,
  );

  protected static $emittedEvents = array('performance');

  protected static $timers = array();

  protected $pendingGetMultiple;

  public function __construct(EventEmitter $emitter) {
    $this->emitter = $emitter;
  }

  protected static function getTimerId($channel, $cids) {
    $args = array_unshift($cids, $channel);
    $timer_id = serialize($args);

    return $timer_id;
  }

  /**
   * @param string $channel
   * @param string $cid
   */
  public function beforeClear($channel) {
    $timer_id = static::getTimerId($channel, array());
    static::$timers[$timer_id] = microtime(TRUE);
  }

  /**
   * @param string $channel
   * @param string $cid
   * @param mixed $data
   *
   * @return array
   */
  public function afterClear($channel) {
    $timer_id = static::getTimerId($channel, array());
    $perfInfo = array(
      'subscriber' => static::NAME,
      'op' => 'clear',
      'bin' => $channel,
      'delay' => (microtime(TRUE) - static::$timers[$timer_id]) * 1E3,
    );
    unset(static::$timers[$timer_id]);

    $this->emitter->emit('performance', $channel, $perfInfo);

    return $perfInfo;
  }

  /**
   * @param $channel
   * @param $cid
   */
  public function beforeGet($channel, $cid) {
    $timer_id = static::getTimerId($channel, array($cid));
    static::$timers[$timer_id] = microtime(TRUE);
  }

  /**
   * @param string $channel
   * @param string $cid
   * @param mixed $value
   *
   * @return array
   */
  public function afterGet($channel, $cid, $value) {
    $timer_id = static::getTimerId($channel, array($cid));
    $perfInfo = array(
      'subscriber' => static::NAME,
      'op' => 'get',
      'bin' => $channel,
      'requested' => array($cid),
      'delay' => (microtime(TRUE) - static::$timers[$timer_id]) * 1E3,
    );
    unset(static::$timers[$timer_id]);

    // Most backends will use at least 1 32-bit word to return FALSE.
    $missInfo['size'] = ($value === FALSE) ? 4 : strlen(serialize($value->data));

    $this->emitter->emit('performance', $channel, $perfInfo);

    return $perfInfo;
  }

  /**
   * @param $channel
   * @param $cids
   */
  public function beforeGetMultiple($channel, $cids) {
    $timer_id = static::getTimerId($channel, $cids);
    $this->pendingGetMultiple[$channel] = $cids;
    static::$timers[$timer_id] = microtime(TRUE);
  }

  /**
   * @param string $channel
   * @param string[] $missed_cids
   * @param mixed[] $result
   *
   * @return array
   */
  public function afterGetMultiple($channel, $missed_cids, $result) {
    $cids = $this->pendingGetMultiple[$channel];
    $timer_id = static::getTimerId($channel, $cids);
    $perfInfo = array(
      'subscriber' => static::NAME,
      'op' => 'getMultiple',
      'bin' => $channel,
      'requested' => $cids,
      'delay' => (microtime(TRUE) - static::$timers[$timer_id]) * 1E3,
    );
    unset(static::$timers[$timer_id]);

    // Most backends will use at least 1 32-bit word to return FALSE.
    $size = 4 * count($missed_cids);

    $this->emitter->emit('performance', $channel, $perfInfo);

    return $perfInfo;
  }

  /**
   * @param string $channel
   */
  public function beforeIsEmpty($channel) {
    $timer_id = static::getTimerId($channel, array());
    static::$timers[$timer_id] = microtime(TRUE);
  }

  /**
   * @param string $channel
   *
   * @return array
   */
  public function afterIsEmpty($channel) {
    $timer_id = static::getTimerId($channel, array());
    $perfInfo = array(
      'subscriber' => static::NAME,
      'op' => 'isEmpty',
      'bin' => $channel,
      'delay' => (microtime(TRUE) - static::$timers[$timer_id]) * 1E3,
    );
    unset(static::$timers[$timer_id]);

    $this->emitter->emit('performance', $channel, $perfInfo);

    return $perfInfo;
  }

  /**
   * @param string $channel
   * @param string $cid
   */
  public function beforeSet($channel, $cid) {
    $timer_id = static::getTimerId($channel, array($cid));
    static::$timers[$timer_id] = microtime(TRUE);
  }

  /**
   * @param string $channel
   * @param string $cid
   * @param mixed $data
   *
   * @return array
   */
  public function afterSet($channel, $cid, $data) {
    $timer_id = static::getTimerId($channel, array($cid));
    $perfInfo = array(
      'subscriber' => static::NAME,
      'op' => 'set',
      'bin' => $channel,
      'requested' => array($cid),
      'delay' => (microtime(TRUE) - static::$timers[$timer_id]) * 1E3,
      'size' => strlen(serialize($data)),
    );
    unset(static::$timers[$timer_id]);

    $this->emitter->emit('performance', $channel, $perfInfo);

    return $perfInfo;
  }

  /**
   * {@inheritdoc}
   */
  public static function getEmittedEvents() {
    return static::$emittedEvents;
  }
}

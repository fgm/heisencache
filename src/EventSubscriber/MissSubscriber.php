<?php

namespace Drupal\heisencache\EventSubscriber;

use Drupal\heisencache\Event\EventInterface;
use Drupal\heisencache\Event\MissEvent;
use Drupal\heisencache\HeisencacheServiceProvider as H;

/**
 * Class MissSubscriber tracks cache get[_multiple] calls resulting in a MISS.
 *
 * @package Drupal\heisencache\EventSubscriber
 */
class MissSubscriber extends ConfigurableListenerBase implements EventSourceInterface {

  use EventSourceTrait;

  /**
   * Events
   */
  const MISS = EventInterface::POST . '_' . H::MODULE . '_miss';
  const MISS_MULTIPLE = EventInterface::POST . '_' . H::MODULE . '_miss_multiple';

  const NAME = "misses";

  protected static $emittedEvents = [
    self::MISS,
    self::MISS_MULTIPLE,
  ];

  protected static $subscribedEvents = [
    EventInterface::POST . '.' . EventInterface::BACKEND_GET =>  1,
    EventInterface::POST . '.' . EventInterface::BACKEND_GET_MULTIPLE=> 1,
    EventInterface::PRE . '.' . EventInterface::BACKEND_GET_MULTIPLE => 1,
  ];

  protected $multipleCids = [];

  /**
   * @param string $channel
   * @param string $cid
   * @param mixed $value
   */
  public function afterGet($channel, $cid, $value) {
    if ($value !== FALSE) {
      return;
    }
    $missInfo = array(
      'misses' => [$cid],
      'requested' => [$cid],
    );
    $event = new MissEvent($channel, EventInterface::POST, $missInfo);
    $this->dispatcher()->dispatch(self::MISS, $event);
  }

  /**
   * @param string $channel
   * @param string[] $missed
   *
   * @return array
   */
  public function afterGetMultiple($channel, $missed) {
    if (empty($missed)) {
      return;
    }

    $requested = $this->multipleCids;
    $this->multipleCids = array();
    $missInfo = array(
      'misses' => $missed,
      'requested' => $requested,
    );
    $event = new MissEvent($channel, EventInterface::POST, $missInfo);
    $this->dispatcher()->dispatch(self::MISS_MULTIPLE, $event);
  }

  public function beforeGetMultiple($channel, $cids) {
    $this->multipleCids = $cids;
  }

}

<?php

namespace Drupal\heisencache\EventSubscriber;

use Drupal\heisencache\Event\EventInterface;
use Drupal\heisencache\Event\MissEvent;
use Drupal\heisencache\HeisencacheServiceProvider as H;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

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
  const MISS = EventInterface::POST . '_miss';
  const MISS_MULTIPLE = EventInterface::POST . '_miss_multiple';

  const NAME = "misses";

  protected static $emittedEvents = [
    self::MISS,
    self::MISS_MULTIPLE,
  ];

  protected $multipleCids = [];

  public function __construct(array $events = []) {
    parent::__construct($events);
  }

  /**
   * @param string $channel
   * @param string $cid
   * @param mixed $value
   */
  public function afterBackendGet($channel, $cid, $value) {
    if ($value !== FALSE) {
      return;
    }
    $missInfo = [
      'misses' => [$cid],
      'requested' => [$cid],
    ];
    $event = new MissEvent($channel, EventInterface::POST, $missInfo);
    $this->dispatcher()->dispatch(self::MISS, $event);
  }

  /**
   * @param string $channel
   * @param string[] $missed
   *
   * @return array
   */
  public function afterBackendGetMultiple($channel, $missed) {
    if (empty($missed)) {
      return;
    }

    $requested = $this->multipleCids;
    $this->multipleCids = [];
    $missInfo = [
      'misses' => $missed,
      'requested' => $requested,
    ];
    $event = new MissEvent($channel, EventInterface::POST, $missInfo);
    $this->dispatcher()->dispatch(self::MISS_MULTIPLE, $event);
  }

  public function beforeBackendGetMultiple($channel, $cids) {
    $this->multipleCids = $cids;
  }

  public static function describe(): Definition {
    $def = parent::describe()
      ->addArgument(new Reference(H::LOGGER))
    ;
    $def->replaceArgument(0, [
      EventInterface::POST . EventInterface::BACKEND_GET,
      EventInterface::POST . EventInterface::BACKEND_GET_MULTIPLE,
      EventInterface::PRE . EventInterface::BACKEND_GET_MULTIPLE,
    ]);
    return $def;
  }

}

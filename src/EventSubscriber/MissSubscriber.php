<?php

namespace Drupal\heisencache\EventSubscriber;

use Drupal\heisencache\Event\BackendGet;
use Drupal\heisencache\Event\BackendGetMultiple;
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
  const MISS = H::MODULE . '.' . EventInterface::POST . '_miss';
  const MISS_MULTIPLE = H::MODULE . '.' . EventInterface::POST . '_miss_multiple';

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
  public function afterBackendGet(BackendGet $event) {
    $data = $event->data();
    if (!empty($data['result'])) {
      return;
    }
    $cid = $data['cid'] ?? NULL;
    $missInfo = [
      'misses' => [$cid],
      'requested' => [$cid],
    ];
    $event = new MissEvent($event->bin, EventInterface::POST, $missInfo);
    $this->dispatcher()->dispatch(self::MISS, $event);
    $this->cid = NULL;
  }

  /**
   * @param string $channel
   * @param string[] $missed
   *
   * @return array
   */
  public function afterBackendGetMultiple(BackendGetMultiple $event) {
    $data = $event->data();
    if (count($data['result']) == count($this->multipleCids)) {
      return;
    }

    $result = array_flip($data['result'] ?? []);
    $requested = $this->multipleCids;
    $missed = array_diff($requested, $result);
    $missInfo = [
      'misses' => $missed,
      'requested' => $requested,
    ];
    $event = new MissEvent($event->bin, EventInterface::POST, $missInfo);
    $this->dispatcher()->dispatch(self::MISS_MULTIPLE, $event);
    $this->multipleCids = [];
  }

  public function beforeBackendGetMultiple(BackendGetMultiple $event) {
    $this->multipleCids = $event->data()['cids'];
  }

  public static function describe(array $knownEvents = []): Definition {
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

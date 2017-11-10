<?php

namespace Drupal\heisencache\Event;

use Drupal\heisencache\EventSubscriber\MissSubscriber;

/**
 * Class MissEvent represents an after-miss(multiple) event.
 *
 * @package Drupal\heisencache\Event
 */
class MissEvent extends EventBase {

  /**
   * Were all keys missed ?
   *
   * @var bool
   */
  public $fullMiss;

  /**
   * Missed cids.
   *
   * @var array
   */
  public $misses;

  /**
   * Requested cids.
   *
   * @var array
   */
  public $requested;

  /**
   * The event source.
   *
   * @var string
   */
  public $source;

  /**
   * MissEvent constructor.
   *
   * @param string $bin
   *   The cache bin.
   * @param string $kind
   *   The event kind. Should be self::POST for this event.
   * @param array $data
   *   The miss information array.
   */
  public function __construct($bin, $kind = self::PRE, array $data = []) {
    parent::__construct($bin, $kind, $data);

    $this->misses = $data['misses'] ?? [];
    $this->requested = $data['requested'] ?? [];
    $this->source = $data['source'] ?? MissSubscriber::NAME;

    $this->fullMiss = $this->requested == $this->misses;
  }

}

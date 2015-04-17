<?php
/**
 * @file
 * FactoryGetEvent.php
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2015 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace Drupal\heisencache\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class FactoryGetEvent
 *
 * @package Drupal\heisencache\Event
 *
 * This event is triggered before and after the cache factory get a bin.
 */
class FactoryGetEvent extends Event {
  /**
   * @var string
   *   No need for getter/setter: no side effects.
   */
  public $bin;

  public function __construct($bin)  {
    $this->bin = $bin;
  }
}

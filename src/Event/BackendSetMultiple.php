<?php
/**
 * @file
 * BackendSetMultiple.php
 *
 * @copyright (c) 2015-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace Drupal\heisencache\Event;

/**
 * Class BackendSetMultiple: triggered pre/post multiple-items set.
 *
 * @package Drupal\heisencache\Event
 */
class BackendSetMultiple extends EventBase {

  public $name = 'backend set multiple';
}

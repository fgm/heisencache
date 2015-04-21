<?php
/**
 * @file
 * BackendSetMultiple.php
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2015 Ouest Systèmes Informatiques (OSInet).
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

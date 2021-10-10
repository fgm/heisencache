<?php

declare(strict_types=1);

namespace Drupal\heisencache\Event;

/**
 * Class BackendSetMultiple: triggered pre/post multiple-items set.
 *
 * @package Drupal\heisencache\Event
 *
 * @copyright (c) 2015-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
class BackendSetMultiple extends EventBase {

  public $name = 'backend set multiple';

}

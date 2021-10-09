<?php

declare(strict_types=1);

namespace Drupal\heisencache\tests;

use Drupal\heisencache\EventSubscriber\EventSubscriberInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * MockEventSubscriberInterface is the combination of a mock and a subscriber.
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
interface MockEventSubscriberInterface extends EventSubscriberInterface, MockObject {

}

<?php
/**
 * @file
 *   MockEventSubscriberInterface.php
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace Drupal\heisencache\tests;

use Drupal\heisencache\EventSubscriberInterface;
use PHPUnit\Framework\MockObject\MockObject;

interface MockEventSubscriberInterface extends EventSubscriberInterface, MockObject {

}

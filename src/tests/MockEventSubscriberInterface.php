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

namespace OSInet\Heisencache\tests;


use OSInet\Heisencache\EventSubscriberInterface;

interface MockEventSubscriberInterface extends EventSubscriberInterface, \PHPUnit_Framework_MockObject_MockObject {

}
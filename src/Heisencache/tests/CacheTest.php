<?php
/**
 * @file
 * Unit tests for the Cache class.
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache\tests;

use OSInet\Heisencache\Cache;
use OSInet\Heisencache\Config;

class CacheTest extends \PHPUnit_Framework_TestCase {

  /**
   * Ensure Cache::get() actually returns the data from the original handler.
   *
   * https://github.com/FGM/heisencache/issues/4
   */
  public function testGet4() {
    $expected = "some random string";
    $cid = "testGet4";
    $bin = 'cache';

    // Create a mock DB cache which will return $expected.
    $handler = $this->getMockBuilder('\DrupalDatabaseCache')
      ->setMethods(array('get'))
      ->setConstructorArgs(array($bin))
      ->getMock();
    $handler->expects($this->once())
      ->method('get')
      ->will($this->returnValue($expected));

    // Create a fake config
    if (!isset($GLOBALS['conf'])) {
      $GLOBALS['conf'] = array();
    }
    $config = Config::instance(array());
    $GLOBALS['conf'] = $config->override();

    // Override default override with our mock cache. Since this is not a public
    // property and there is no setter, force it using reflection.
    $core_cache = \Drupal::cache('cache');
    $dispatcher = \Drupal::service('event_dispatcher');
    $cache = new Cache('cache', $core_cache, $dispatcher);
    $rp = new \ReflectionProperty($cache, 'handler');
    $rp->setAccessible(TRUE);
    $rp->setValue($cache, $handler);

    $actual = $cache->get($cid);
    $this->assertNotEquals($actual, FALSE, "Cache returns permanent stored data");
  }
}

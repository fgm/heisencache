<?php
/**
 * @file
 * Unit tests for the MissSubscriber class.
 *
 * @author: marand
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache\tests;


use OSInet\Heisencache\MissSubscriber;

class MissSubscriberTest extends \PHPUnit_Framework_TestCase {

  public function testGetHit() {
    $sub = new MissSubscriber();
    $sub->afterGet('k', 'v');
    $expected = serialize(NULL);
    $this->expectOutputRegex('/' . $expected . '/');
  }

  public function testGetMiss() {
    $sub = new MissSubscriber();
    $sub->afterGet('somekey', FALSE);

    $this->expectOutputRegex('/s:6:"misses";a:.*somekey/');
//    $expected = serialize(NULL);
//    $this->expectOutputRegex('/' . $expected . '/');
  }

  public function testGetMultipleWithMisses() {
    $initial_cids = array('k1', 'k2', 'k3');
    $missed_cids = array('k1', 'k3');

    $sub = new MissSubscriber();
    $sub->beforeGetMultiple($initial_cids);
    $sub->afterGetMultiple($missed_cids);

    $this->expectOutputRegex('/s:6:"misses";a:.*(k1.*k3)|(k3.*k1)/');
  }

  public function testGetMultipleWithoutMisses() {
    $initial_cids = array('k1', 'k2', 'k3');
    $missed_cids = array();

    $sub = new MissSubscriber();
    $sub->beforeGetMultiple($initial_cids);
    $sub->afterGetMultiple($missed_cids);
    $expected = serialize(NULL);
    $this->expectOutputRegex('/' . $expected . '/');
  }
}

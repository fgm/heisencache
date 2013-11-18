<?php
/**
 * @file
 * A subscriber on cache miss events.
 *
 * @author: marand
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache;


class MissSubscriber extends BaseEventSubscriber {

  const NAME = "misses";

  protected $events = array(
    'afterGet' =>  1,
    'afterGetMultiple' => 1,
    'beforeGetMultiple' => 1,
  );

  protected $multipleCids = array();

  public function afterGet($cid, $value) {
    if ($value === FALSE) {
      $ret = array(
        'subscriber' => static::NAME,
        'op' => 'get',
        'requested' => array($cid),
        'misses' => array($cid),
      );
      echo serialize($ret) . "\n";
    }
  }

  public function afterGetMultiple($missed_cids) {
    $requested = $this->multipleCids;
    $this->multipleCids = array();

    if (!empty($missed_cids)) {
      $ret = array(
        'subscriber' => static::NAME,
        'op' => 'get_multiple',
        'requested' => $requested,
        'misses' => $missed_cids,
      );
      echo serialize($ret) . "\n";
    };
  }

  public function beforeGetMultiple($cids) {
    $this->multipleCids = $cids;
  }
}

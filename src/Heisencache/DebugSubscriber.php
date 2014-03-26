<?php
/**
 * @file
 * An universal subscriber for debug purposes.
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache;

class DebugSubscriber extends BaseEventSubscriber {
  public function show() {
    $stack = debug_backtrace(FALSE);
    $caller = $stack[1]['function'];
    $args = func_get_args();
    $arg0 = is_string($args[0]) ? $args[0] : 'unprintable';
    echo "$caller({$arg0})<br />\n";
  }

  public function __construct(array $events = NULL) {
    if (!isset($events)) {
      $events = array_merge(Cache::getEmittedEvents(), MissSubscriber::getEmittedEvents());
    }
    foreach ($events as $eventName) {
      $this->addEvent($eventName);
    }
  }

  public function beforeGet($channel, $key) {
   $this->show($key);
  }

  public function afterGet($channel, $key, $value) {
   $this->show($key, $value);

  }

  public function beforeGetMultiple($channel, array $keys) {
    $this->show(implode(", ", $keys));
  }

  public function afterGetMultiple($channel, array $keys) {
    $this->show(implode(", ", $keys));
  }

  public function beforeSet($channel, $key, $value) {
    $this->show($key, $value);
  }

  public function afterSet($channel, $key, $value) {
    $this->show($key, $value);
  }

  public function beforeClear($channel) {
    $this->show();
  }

  public function afterClear($channel) {
    $this->show();
  }

  public function beforeIsEmpty($channel) {
    $this->show();
  }

  public function afterIsEmpty($channel) {
    $this->show();
  }

  public function miss($channel) {
    $this->show();
  }

  public function missMultiple($channel) {
    $this->show();
  }

  public function onCacheConstruct($channel) {
   $this->show($channel);
  }

  public function onShutdown($channel) {
    $this->show($channel);
  }
}
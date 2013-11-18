<?php
/**
 * @file
 * An universal subscriber for debug purposes.
 *
 * @author: marand
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
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
    echo "$caller({$arg0})<br >\n";
  }

  public static function getAvailableEvents() {
    $myEvents = array(
      'onCacheConstruct',
      'beforeGet',            'afterGet',
      'beforeGetMultiple',    'afterGetMultiple',
      'beforeSet',            'afterSet',
      'beforeClear',          'afterClear',
      'beforeIsEmpty',        'afterIsEmpty',
    );
    return $myEvents;
  }

  public function __construct(array $events = NULL) {
    if (!isset($events)) {
      $events = static::getAvailableEvents();
    }
    foreach ($events as $eventName) {
      $this->addEvent($eventName);
    }
  }

  public function beforeGet($key) {
   $this->show($key);
  }

  public function afterGet($key, $value) {
   $this->show($key, $value);

  }

  public function beforeGetMultiple($keys) {
    $this->show(implode(", ", $keys));
  }

  public function afterGetMultiple($keys) {
    $this->show(implode(", ", $keys));
  }

  public function beforeSet($key, $value) {
    $this->show($key, $value);
  }

  public function afterSet($key, $value) {
    $this->show($key, $value);
  }

  public function beforeClear() {
    $this->show();
  }

  public function afterClear() {
    $this->show();
  }

  public function beforeIsEmpty() {
    $this->show();
  }

  public function afterIsEmpty() {
    $this->show();
  }

  public function onCacheConstruct($bin) {
   $this->show($bin);
  }
} 
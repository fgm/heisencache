<?php

namespace Drupal\heisencache\EventSubscriber;

use Drupal\heisencache\Event\BackendDelete;
use Drupal\heisencache\Event\BackendDeleteAll;
use Drupal\heisencache\Event\BackendDeleteMultiple;
use Drupal\heisencache\Event\BackendGarbageCollection;
use Drupal\heisencache\Event\BackendGet;
use Drupal\heisencache\Event\BackendGetMultiple;
use Drupal\heisencache\Event\BackendInvalidate;
use Drupal\heisencache\Event\BackendInvalidateAll;
use Drupal\heisencache\Event\BackendInvalidateMultiple;
use Drupal\heisencache\Event\BackendSet;
use Drupal\heisencache\Event\BackendSetMultiple;
use Drupal\heisencache\Event\EventBase;
use Drupal\heisencache\Event\FactoryGetEvent;
use Drupal\heisencache\Event\RemoveBin;
use Robo\Task\Docker\Remove;

/**
 * Class DebugSubscriber
 *
 * @package Drupal\heisencache\EventSubscriber
 *
 * FIXME the D7 DebugSubcriber defaulted to the events for H::Cache and
 * MissSubscriber only. This version receives ALL active events for the current
 * configuration, but can still only handle the H, CacheBackendInterface and
 * MissSubscriber events.
 */
class DebugSubscriber extends ConfigurableListenerBase {
  const DELIMITER = ', ';

  public static function color(): array {
    $id = hash("crc32b", $_SERVER['UNIQUE_ID'] ?? '');
    return [$id, '#' . substr($id, 0, 6)];
  }

  public function show(string $bin): void {
    $stack = debug_backtrace(FALSE);
    $caller = $stack[1]['function'];
    $args = func_get_args();
    $arg1 = is_string($args[1]) ? $args[1] : json_encode($args[1]);
    list($id, $color) = $this->color();
    echo "<span style='color: ${color}'>$id: $caller($bin, {$arg1})</span><br />\n";
  }

  public function beforeBackendDelete(BackendDelete $event): void {
    if ($event->kind() !== EventBase::PRE) {
      return;
    }
    $this->show($event->bin, $event->data()['cid']);
  }

  public function afterBackendDelete(BackendDelete $event): void {
    if ($event->kind() !== EventBase::POST) {
      return;
    }
    $this->show($event->bin, $event->data()['cid']);
  }

  public function beforeBackendDeleteAll(BackendDeleteAll $event): void {
    if ($event->kind() !== EventBase::PRE) {
      return;
    }
    $this->show($event->bin);
  }

  public function afterBackendDeleteAll(BackendDeleteAll $event): void {
    if ($event->kind() !== EventBase::POST) {
      return;
    }
    $this->show($event->bin);
  }

  public function beforeBackendDeleteMultiple(BackendDeleteMultiple $event): void {
    if ($event->kind() !== EventBase::PRE) {
      return;
    }
    $this->show($event->bin, implode(static::DELIMITER, $event->data()['keys']));
  }

  public function afterBackendDeleteMultiple(BackendDeleteMultiple $event): void {
    if ($event->kind() !== EventBase::POST) {
      return;
    }
    $this->show($event->bin, implode(static::DELIMITER, $event->data()['keys']));
  }

  public function beforeBackendGarbageCollection(BackendGarbageCollection $event): void {
    if ($event->kind() !== EventBase::PRE) {
      return;
    }
    $this->show($event->bin);
  }

  public function afterBackendGarbageCollection(BackendGarbageCollection $event): void {
    if ($event->kind() !== EventBase::POST) {
      return;
    }
    $this->show($event->bin);
  }

  public function beforeBackendGet(BackendGet $event): void {
    if ($event->kind() !== EventBase::PRE) {
      return;
    }
    $this->show($event->bin, $event->data()['cid']);
  }

  public function afterBackendGet(BackendGet $event): void {
    if ($event->kind() !== EventBase::POST) {
      return;
    }
    $this->show($event->bin, $event->data()['cid'], $event->data()['value']);
  }

  public function beforeBackendGetMultiple(BackendGetMultiple $event): void {
    if ($event->kind() !== EventBase::PRE) {
      return;
    }
    $this->show($event->bin, implode(static::DELIMITER, $event->data()['cids']));
  }

  public function afterBackendGetMultiple(BackendGetMultiple $event): void {
    if ($event->kind() !== EventBase::POST) {
      return;
    }
    $this->show($event->bin, implode(static::DELIMITER, $event->data()['cids']));
  }

  public function beforeBackendInvalidate(BackendInvalidate $event): void {
    if ($event->kind() !== EventBase::PRE) {
      return;
    }
    $this->show($event->bin, $event->data()['cid']);
  }

  public function afterBackendInvalidate(BackendInvalidate $event): void {
    if ($event->kind() !== EventBase::POST) {
      return;
    }
    $this->show($event->bin, $event->data()['cid']);
  }

  public function beforeBackendInvalidateAll(BackendInvalidateAll $event): void {
    if ($event->kind() !== EventBase::PRE) {
      return;
    }
    $this->show($event->bin);
  }

  public function afterBackendInvalidateAll(BackendInvalidateAll $event): void {
    if ($event->kind() !== EventBase::POST) {
      return;
    }
    $this->show($event->bin);
  }

  public function beforeBackendInvalidateMultiple(BackendInvalidateMultiple $event): void {
    if ($event->kind() !== EventBase::PRE) {
      return;
    }
    $this->show($event->bin, implode(static::DELIMITER, $event->data()['cids']));
  }

  public function afterBackendInvalidateMultiple(BackendInvalidateMultiple $event): void {
    if ($event->kind() !== EventBase::POST) {
      return;
    }
    $this->show($event->bin, implode(static::DELIMITER, $event->data()['cids']));
  }

  public function beforeBackendRemoveBin(RemoveBin $event): void {
    if ($event->kind() !== EventBase::PRE) {
      return;
    }
    $this->show($event->bin);
  }

  public function afterBackendRemoveBin(RemoveBin $event): void {
    if ($event->kind() !== EventBase::POST) {
      return;
    }
    $this->show($event->bin);
  }

  public function beforeBackendSet(BackendSet $event): void {
    if ($event->kind() !== EventBase::PRE) {
      return;
    }
    $this->show($event->bin, $event->data()['cid'], $event->data()['value']);
  }

  public function afterBackendSet(BackendSet $event): void {
    if ($event->kind() !== EventBase::POST) {
      return;
    }
    $this->show($event->bin, $event->data()['cid'], $event->data()['value']);
  }

  public function beforeBackendSetMultiple(BackendSetMultiple $event): void {
    if ($event->kind() !== EventBase::PRE) {
      return;
    }
    $this->show($event->bin, $event->data());
  }

  public function afterBackendSetMultiple(BackendSetMultiple $event): void {
    if ($event->kind() !== EventBase::POST) {
      return;
    }
    $this->show($event->bin, $event->data());
  }

  public function onCacheConstruct(FactoryGetEvent $event): void {
    $this->show($event->bin);
  }

  public function onMiss(EventBase $event): void {
    $this->show($event->bin);
  }

  public function onMissMultiple(EventBase $event): void {
    $this->show($event->bin, $event->data()['cids']);
  }

  public function onShutdown(EventBase $event): void {
    $this->show($event->bin);
  }

}

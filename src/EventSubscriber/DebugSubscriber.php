<?php

namespace Drupal\heisencache\EventSubscriber;

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
class DebugSubscriber extends ConfigurableSubscriberBase {
  const DELIMITER = ', ';

  public function show(string $bin): void {
    $stack = debug_backtrace(FALSE);
    $caller = $stack[1]['function'];
    $args = func_get_args();
    $arg1 = is_string($args[1]) ? $args[1] : 'unprintable';
    echo "$caller({$arg1})<br />\n";
  }

  public function beforeDelete(string $bin, string $key): void {
    $this->show($bin, $key);
  }

  public function afterDelete(string $bin, string $key): void {
    $this->show($bin, $key);
  }

  public function beforeDeleteAll(string $bin): void {
    $this->show($bin);
  }

  public function afterDeleteAll(string $bin): void {
    $this->show($bin);
  }

  public function beforeDeleteMultiple(string $bin, array $keys): void {
    $this->show($bin, implode(static::DELIMITER, $keys));
  }

  public function afterDeleteMultiple(string $bin, array $keys): void {
    $this->show($bin, implode(static::DELIMITER, $keys));
  }

  public function afterGarbageCollection(string $bin): void {
    $this->show($bin);
  }

  public function beforeGet(string $bin, string $key): void {
    $this->show($bin, $key);
  }

  public function afterGet(string $bin, string $key, $value): void {
    $this->show($bin, $key, $value);
  }

  public function beforeGetMultiple(string $bin, array $keys): void {
    $this->show($bin, implode(static::DELIMITER, $keys));
  }

  public function afterGetMultiple(string $bin, array $keys): void {
    $this->show($bin, implode(static::DELIMITER, $keys));
  }

  public function beforeInvalidate(string $bin, string $key): void {
    $this->show($bin, $key);
  }

  public function afterInvalidate(string $bin, string $key): void {
    $this->show($bin, $key);
  }

  public function beforeInvalidateAll(string $bin): void {
    $this->show($bin);
  }

  public function afterInvalidateAll(string $bin): void {
    $this->show($bin);
  }

  public function beforeInvalidateMultiple(string $bin, array $keys): void {
    $this->show($bin, implode(static::DELIMITER, $keys));
  }

  public function afterInvalidateMultiple(string $bin, array $keys): void {
    $this->show($bin, implode(static::DELIMITER, $keys));
  }

  public function beforeRemoveBin(string $bin): void {
    $this->show($bin);
  }

  public function afterRemoveBin(string $bin): void {
    $this->show($bin);
  }

  public function beforeSet(string $bin, $key, $value): void {
    $this->show($bin, $key, $value);
  }

  public function afterSet(string $bin, $key, $value): void {
    $this->show($bin, $key, $value);
  }

  public function beforeSetMultiple(string $bin, array $items): void {
    $this->show($bin, $items);
  }

  public function afterSetMultiple(string $bin, array $items): void {
    $this->show($bin, $items);
  }

  public function onCacheConstruct(string $bin): void {
    $this->show($bin);
  }

  public function onMiss(string $bin, string $key): void {
    $this->show($bin);
  }

  public function onMissMultiple(string $bin, array $keys): void {
    $this->show($bin);
  }

  public function onShutdown(string $bin): void {
    $this->show($bin);
  }

}

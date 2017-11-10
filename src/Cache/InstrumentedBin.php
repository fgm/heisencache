<?php

namespace Drupal\heisencache\Cache;

use Drupal\Core\Cache\Cache as CoreCache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\heisencache\Event\BackendConstruct;
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
use Drupal\heisencache\Event\EventInterface;
use Drupal\heisencache\EventSubscriber\EventSourceInterface;
use Drupal\heisencache\Event\RemoveBin;
use Drupal\heisencache\EventSubscriber\EventSourceTrait;
use Drupal\heisencache\HeisencacheServiceProvider as H;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class InstrumentedBin wraps event sources around cache handlers.
 *
 * @package Drupal\heisencache\Cache
 */
class InstrumentedBin implements CacheBackendInterface, EventSourceInterface {

  use EventSourceTrait;

  /**
   * The name of the cache bin.
   *
   * @var string
   */
  protected $bin;

  /**
   * An array of event names.
   *
   * @var string[]
   */
  protected static $events = NULL;

  /**
   * The decorated cache backend instance for the bin.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $decorated;

  /**
   * Constructs an InstrumentedBin object.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $decorated
   *   The original cache backend to instrument.
   * @param string $bin
   *   The cache bin for which the backend is instantiated.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   The event_dispatcher service.
   */
  public function __construct(
    CacheBackendInterface $decorated,
    string $bin,
    EventDispatcherInterface $eventDispatcher
  ) {
    $this->bin = $bin;
    $this->decorated = $decorated;
    $this->eventDispatcher = $eventDispatcher;

    $event = new BackendConstruct($bin);
    $this->dispatch($event);
  }

  /**
   * {@inheritdoc}
   */
  public function delete($cid) {
    $event = new BackendDelete($this->bin, EventInterface::PRE, compact('cid'));
    $this->dispatch($event);
    $this->decorated->delete($cid);
    $this->dispatch($event->setPost());
  }

  /**
   * {@inheritdoc}
   */
  public function deleteAll() {
    $event = new BackendDeleteAll($this->bin);
    $this->dispatch($event);
    $this->decorated->deleteAll();
    $this->dispatch($event->setPost());
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMultiple(array $cids) {
    $event = new BackendDeleteMultiple($this->bin, EventInterface::PRE, compact('cids'));
    $this->dispatch($event);
    $this->decorated->deleteMultiple($cids);
    $this->dispatch($event->setPost());
  }

  /**
   * {@inheritdoc}
   */
  public function garbageCollection() {
    $event = new BackendGarbageCollection($this->bin);
    $this->dispatch($event);
    $this->decorated->garbageCollection();
    $this->dispatch($event->setPost());
  }

  /**
   * {@inheritdoc}
   */
  public function get($cid, $allow_invalid = FALSE) {
    $event = new BackendGet($this->bin, EventInterface::PRE, compact('cid', 'allow_invalid'));
    $this->dispatch($event);
    $result = $this->decorated->get($cid, $allow_invalid);
    $this->dispatch($event->setPost()->setData([
      'result' => $result,
    ]));
    return $result;
  }

  /**
   * List the events emitted by a bin.
   *
   * They are the before/after hooks for all backend methods, plus construction
   * and terminate.
   *
   * @return \string[]
   *   The array of available events.
   */
  public static function getEmittedEvents() {
    if (!isset(static::$events)) {
      $methods = get_class_methods(CacheBackendInterface::class);
      $events = [
        H::MODULE . '.onTerminate',
      ];
      foreach ($methods as $method) {
        $events[] = H::MODULE . '.' . EventInterface::PRE . ucfirst($method);
        $events[] = H::MODULE . '.' . EventInterface::POST . ucfirst($method);
      }
      static::$events = $events;
    }

    return static::$events;
  }

  /**
   * {@inheritdoc}
   */
  public function getMultiple(&$cids, $allow_invalid = FALSE) {
    $event = new BackendGetMultiple($this->bin, EventInterface::PRE, compact('cids', 'allow_invalid'));
    $this->dispatch($event);
    $result = $this->decorated->getMultiple($cids, $allow_invalid);
    $this->dispatch($event->setPost()->setData([
      'result' => $result,
    ]));

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function invalidate($cid) {
    $event = new BackendInvalidate($this->bin, EventInterface::PRE, compact('cid'));
    $this->dispatch($event);
    $this->decorated->invalidate($cid);
    $this->dispatch($event->setPost());

  }

  /**
   * {@inheritdoc}
   */
  public function invalidateAll() {
    $event = new BackendInvalidateAll($this->bin);
    $this->dispatch($event);
    $this->decorated->invalidateAll();
    $this->dispatch($event->setPost());
  }

  /**
   * {@inheritdoc}
   */
  public function invalidateMultiple(array $cids) {
    $event = new BackendInvalidateMultiple($this->bin, EventInterface::PRE, compact('cids'));
    $this->dispatch($event);
    $this->decorated->invalidateMultiple($cids);
    $this->dispatch($event->setPost());
  }

  /**
   * {@inheritdoc}
   */
  public function removeBin() {
    $event = new RemoveBin($this->bin);
    $this->dispatch($event);
    $this->decorated->removeBin();
    $this->dispatch($event->setPost());
  }

  /**
   * {@inheritdoc}
   */
  public function set($cid, $data, $expire = CoreCache::PERMANENT, array $tags = []) {
    $event = new BackendSet($this->bin, EventInterface::PRE, compact('cid', 'data', 'expire', 'tags'));
    $this->dispatch($event);
    $this->decorated->set($cid, $data, $expire, $tags);
    $this->dispatch($event->setPost());
  }

  /**
   * {@inheritdoc}
   */
  public function setMultiple(array $items = []) {
    $event = new BackendSetMultiple($this->bin, EventInterface::PRE, compact('items'));
    $this->dispatch($event);
    $this->decorated->setMultiple($items);
    $this->dispatch($event->setPost());
  }

}

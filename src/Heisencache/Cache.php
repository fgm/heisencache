<?php
/**
 * @file
 *   Cache.php : The cache class used by Drupal.
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache;

use Drupal\Core\Cache\Cache as CoreCache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\heisencache\Event\BackendDelete;
use Drupal\heisencache\Event\BackendDeleteAll;
use Drupal\heisencache\Event\BackendDeleteMultiple;
use Drupal\heisencache\Event\BackendGet;
use Drupal\heisencache\Event\BackendGetMultiple;
use Drupal\heisencache\Event\BackendInvalidate;
use Drupal\heisencache\Event\BackendInvalidateAll;
use Drupal\heisencache\Event\BackendInvalidateMultiple;
use Drupal\heisencache\Event\BackendSet;
use Drupal\heisencache\Event\BackendSetMultiple;
use Drupal\heisencache\Event\BackendConstruct;
use Drupal\heisencache\Event\EventDispatcherTrait;
use Drupal\heisencache\Event\EventInterface;
use Drupal\heisencache\Event\BackendGarbageCollection;
use Drupal\heisencache\Event\RemoveBin;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Cache
 *
 * @package OSInet\Heisencache
 */
class Cache implements CacheBackendInterface, EventSourceInterface {

  use EventDispatcherTrait;

  /**
   * @var string
   *   The name of the cache bin.
   */
  protected $bin;

  /**
   * @var string[]
   *   An array of event names.
   */
  protected static $events = NULL;

  /**
   * @var \Drupal\Core\Cache\CacheBackendInterface
   *   The decorated cache backend instance for the bin.
   */
  protected $decorated;

  public function __construct($bin, CacheBackendInterface $decorated, EventDispatcherInterface $dispatcher) {
    $this->bin = $bin;
    $this->decorated = $decorated;
    $this->dispatcher = $dispatcher;

    $event = new BackendConstruct($bin, EventInterface::IN);
    $this->dispatch($event);
  }

  /**
   * {@inheritdoc}
   */
  public function delete($cid) {
    $event = new BackendDelete($this->bin, EventInterface::PRE, get_defined_vars());
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
    $event = new BackendDeleteMultiple($this->bin, EventInterface::PRE, get_defined_vars());
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
    $event = new BackendGet($this->bin, EventInterface::PRE, get_defined_vars());
    $this->dispatch($event);
    $result = $this->decorated->get($cid, $allow_invalid);
    $this->dispatch($event->setPost()->setData([
      'result' => $result,
    ]));
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getMultiple(&$cids, $allow_invalid = FALSE) {
    $event = new BackendGetMultiple($this->bin, EventInterface::PRE, get_defined_vars());
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
    $event = new BackendInvalidate($this->bin, EventInterface::PRE, get_defined_vars());
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
    $event = new BackendInvalidateMultiple($this->bin, EventInterface::PRE, get_defined_vars());
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
  public function set($cid, $data, $expire = CoreCache::PERMANENT, array $tags = array()) {
    $event = new BackendSet($this->bin, EventInterface::PRE, get_defined_vars());
    $this->dispatch($event);
    $this->decorated->set($cid, $data, $expire, $tags);
    $this->dispatch($event->setPost());
  }

  /**
   * {@inheritdoc}
   */
  public function setMultiple(array $items) {
    $event = new BackendSetMultiple($this->bin, EventInterface::PRE, get_defined_vars());
    $this->dispatch($event);
    $this->decorated->setMultiple($items);
    $this->dispatch($event->setPost());
  }

  // ===== Heisencache 7 methods ==============================

  /**
   * @return \string[]
   *   The array of available events.
   */
  public static function getEmittedEvents() {
    if (!isset(static::$events)) {
      $methods = get_class_methods('\DrupalCacheInterface');
      $events = array('onCacheConstruct', 'onShutdown');
      foreach ($methods as $method) {
        $events[] = 'before' . ucfirst($method);
        $events[] = 'after' . ucfirst($method);
      }
      static::$events = $events;
    }

    return static::$events;
  }
}

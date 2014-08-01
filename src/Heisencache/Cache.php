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

/**
 * Class Cache
 *
 * @package OSInet\Heisencache
 */
class Cache implements \DrupalCacheInterface, EventSourceInterface {
  /**
   * @var
   */
  protected $bin;

  /**
   * @var string[]
   *   An array of event names.
   */
  protected static $events = NULL;

  /**
   * @var \DrupalCacheInterface string
   *   The cache instance actually implementing caching for this bin.
   */
  protected $handler;

  /**
   * @var \OSInet\Heisencache\EventEmitter
   */
  protected $emitter;

  /**
   * By the Drupal 7 cache API, cache constructors do not receive any parameter.
   *
   * So we have to fetch the configuration instead of receiving it.
   */
  public function __construct($bin) {
    $this->bin = $bin;
    $config = Config::instance();
    $this->handler = $config->getCacheHandler($bin);
    $this->emitter = $config->getEmitter();
    $this->emitter->emit('onCacheConstruct', $bin);
  }

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

  /**
   * Returns data from the persistent cache.
   *
   * Data may be stored as either plain text or as serialized data. cache_get()
   * will automatically return unserialized objects and arrays.
   *
   * @see cache_get()
   *
   * @param string $cid
   *   The cache ID of the data to retrieve.
   *
   * @return mixed
   *   The cache or FALSE on failure.
   */
  public function get($cid) {
    $this->emitter->emit('beforeGet', $this->bin, $cid);
    $result = $this->handler->get($cid);
    $this->emitter->emit('afterGet', $this->bin, $cid, $result);
    return $result;
  }

  /**
   * Returns data from the persistent cache when given an array of cache IDs.
   *
   * @param $cids
   *   An array of cache IDs for the data to retrieve. This is passed by
   *   reference, and will have the IDs successfully returned from cache
   *   removed.
   *
   * @return array
   *   An array of the items successfully returned from cache indexed by cid.
   */
  public function getMultiple(&$cids) {
    $this->emitter->emit('beforeGetMultiple', $this->bin, $cids);
    $result = $this->handler->getMultiple($cids);
    $this->emitter->emit('afterGetMultiple', $this->bin, $cids, $result);
    return $result;
  }

  /**
   * Stores data in the persistent cache.
   *
   * @param $cid
   *   The cache ID of the data to store.
   * @param $data
   *   The data to store in the cache. Complex data types will be automatically
   *   serialized before insertion.
   *   Strings will be stored as plain text and not serialized.
   * @param $expire
   *   One of the following values:
   *   - CACHE_PERMANENT: Indicates that the item should never be removed unless
   *     explicitly told to using cache_clear_all() with a cache ID.
   *   - CACHE_TEMPORARY: Indicates that the item should be removed at the next
   *     general cache wipe.
   *   - A Unix timestamp: Indicates that the item should be kept at least until
   *     the given time, after which it behaves like CACHE_TEMPORARY.
   */
  public function set($cid, $data, $expire = CACHE_PERMANENT) {
    $this->emitter->emit('beforeSet', $this->bin, $cid, $data, $expire);
    $this->handler->set($cid, $data, $expire);
    $this->emitter->emit('afterSet', $this->bin, $cid, $data, $expire);
  }

  /**
   * Expires data from the cache.
   *
   * If called without arguments, applicable entries will be cleared from the
   * cache_page and cache_block bins.
   *
   * @param $cid
   *   If set, the cache ID or an array of cache IDs. Otherwise, all cache
   *   entries that can expire are deleted. The $wildcard argument will be
   *   ignored if set to NULL.
   * @param $wildcard
   *   If TRUE, the $cid argument must contain a string value and cache IDs
   *   starting with $cid are deleted in addition to the exact cache ID
   *   specified by $cid. If $wildcard is TRUE and $cid is '*', the entire
   *   cache is emptied.
   */
  public function clear($cid = NULL, $wildcard = FALSE) {
    $this->emitter->emit('beforeClear', $this->bin, $cid, $wildcard);
    $this->handler->clear($cid, $wildcard);
    $this->emitter->emit('afterClear', $this->bin, $cid, $wildcard);
  }

  /**
   * Checks if a cache bin is empty.
   *
   * A cache bin is considered empty if it does not contain any valid data for
   * any cache ID.
   *
   * @return bool
   *   TRUE if the cache bin specified is empty.
   */
  public function isEmpty() {
    $this->emitter->emit('beforeIsEmpty', $this->bin);
    $result = $this->handler->isEmpty();
    $this->emitter->emit('afterIsEmpty', $this->bin, $result);
    return $result;
  }
}

<?php
/**
 * @file
 *   Config.php
 *
 * @author: marand
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache;

/**
 * Class Config.
 *
 * Configure Heisencache from the original cache chain.
 *
 * @package OSInet\Heisencache
 */
class Config {
  const CACHE_CLASS = 'OSInet\Heisencache\Cache';
  const VAR_CACHE_DEFAULT_CLASS = 'cache_default_class';
  const VAR_CACHE_CLASS_PREFIX = 'cache_class_';

  /**
   * @var string[]
   *   The bins exposed to the Drupal cache API.
   */
  protected $visible_bins = array();

  /**
   * @var array
   *   The actual cache classes to use according to settings.php.
   */
  protected $actual_bins = array();

  /**
   * @var array
   *   A local copy of the original raw $conf.
   */
  protected $conf;

  /**
   * @var string
   *   The name of the original default cache class, used as:
   *     $class = variable_get('cache_default_class', 'DrupalDatabaseCache');
   */
  protected $defaultClass;

  /**
   * @var \OSInet\Heisencache\EventEmitter
   */
  protected $emitter;

  /**
   * @var \OSInet\Heisencache\Config
   */
  protected static $instance;

  protected function __construct($conf) {
    $this->conf = $conf;
    $this->emitter = new EventEmitter();
  }

  protected function __clone() {
    throw new \Exception('Heisencache configuration should not be cloned.');
  }

  /**
   * @return EventEmitter
   */
  public function getEmitter() {
    return $this->emitter;
  }

  public function getSrcDir() {
    return __DIR__;
  }

  public static function instance($conf = array()) {
    if (!isset(static::$instance)) {
      static::$instance = new static($conf);
    }
    return static::$instance;
  }

  protected function overrideDefaultCacheClass() {
    $this->defaultClass = isset($this->conf[self::VAR_CACHE_DEFAULT_CLASS])
      ? $this->conf[self::VAR_CACHE_DEFAULT_CLASS]
      : 'DrupalDatabaseCache';

    return static::CACHE_CLASS;
  }

  protected function overrideCacheClasses() {
    $len = strlen(self::VAR_CACHE_CLASS_PREFIX);

    foreach ($this->conf as $bin => $class) {
      if (!strncmp($bin, self::VAR_CACHE_CLASS_PREFIX, $len)) {
        $this->visible_bins[$bin] = static::CACHE_CLASS;
        $this->actual_bins[$bin] = $class;
      }
    }

    return $this->visible_bins;
  }

  /**
   * Set up cache overrides
   *
   * - insert Heisencache\Cache as the sole cache class
   * - register pre-existing cache configurations into the Cache instance
   *   - cache_class_*
   *   - cache_class_default_class
   *
   * @return array
   *   The overridden configuration.
   */
  public function override() {
    $cacheConf = array_merge(array(
      self::VAR_CACHE_DEFAULT_CLASS => $this->overrideDefaultCacheClass(),
    ), $this->overrideCacheClasses());

    $conf = array_merge($GLOBALS['conf'], $cacheConf);
    return $conf;
  }
}
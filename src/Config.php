<?php
/**
 * @file
 *   Config.php
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace Drupal\heisencache;

use Drupal\heisencache\Cache\Cache;

/**
 * Class Config.
 *
 * Configure Heisencache from the original cache chain.
 *
 * @package Drupal\heisencache
 */
class Config {

  const CACHE_CLASS = Cache::class;

  const VAR_CACHE_DEFAULT_CLASS = 'cache_default_class';

  const VAR_CACHE_CLASS_PREFIX = 'cache_class_';

  /**
   * @var string[]
   *   The bins exposed to the Drupal cache API.
   */
  protected array $visibleBins = [];

  /**
   * @var array
   *   The actual cache classes to use according to settings.php.
   */
  protected array $actualBins = [];

  /**
   * @var array
   *   A local copy of the original raw $conf.
   */
  protected array $conf;

  /**
   * @var string
   *   The name of the original default cache class, used as:
   *     $class = variable_get('cache_default_class', 'DrupalDatabaseCache');
   */
  protected string $defaultClass;

  /**
   * @var \Drupal\heisencache\EventEmitter
   */
  protected EventEmitter $emitter;

  /**
   * @var \Drupal\heisencache\Config
   */
  protected static $instance;

  protected function __construct($conf) {
    $this->conf = $conf;
    $this->emitter = new EventEmitter();
  }

  /**
   * @throws \Exception
   */
  protected function __clone() {
    throw new \Exception('Heisencache configuration should not be cloned.');
  }

  /**
   * Return an instance of a cache handler for the requested bin.
   *
   * @param string $bin
   *
   * @return mixed
   */
  public function getCacheHandler(string $bin) {
    if (!isset($this->actualBins[$bin])) {
      $this->actualBins[$bin] = new $this->defaultClass($bin);
    }

    $ret = $this->actualBins[$bin];
    return $ret;
  }

  /**
   * @return EventEmitter
   */
  public function getEmitter(): EventEmitter {
    return $this->emitter;
  }

  /**
   * Return the directory in which the Heisencache classes are located.
   *
   * @return string
   */
  public function getSrcDir(): string {
    return __DIR__;
  }

  /**
   * Return the singleton instance of the Heisencache configuration on the site.
   *
   * @param array $conf
   *   The original site settings, prior to override. Only needed for initial
   *   instance creation, ignored in later calls.
   *
   * @return \Drupal\heisencache\Config
   */
  public static function instance(array $conf = []): self {
    if (!isset(static::$instance)) {
      static::$instance = new static($conf);
    }
    return static::$instance;
  }

  /**
   * Initialize the default cache class with Heisencache and save the original.
   *
   * @return string
   */
  protected function overrideDefaultCacheClass(): string {
    $this->defaultClass = $this->conf[self::VAR_CACHE_DEFAULT_CLASS] ?? 'DrupalDatabaseCache';
    return static::CACHE_CLASS;
  }

  /**
   * Save the original cache handler definitions and return Heisencache for
   * them.
   *
   * Instantiate the original cache handlers for later use.
   *
   * @return string[]
   *   A by-bin-name hash of the Heisencache class name.
   */
  protected function overrideCacheClasses(): array {
    $len = strlen(self::VAR_CACHE_CLASS_PREFIX);

    foreach ($this->conf as $bin => $class) {
      if (!strncmp($bin, self::VAR_CACHE_CLASS_PREFIX, $len)) {
        $this->visibleBins[$bin] = static::CACHE_CLASS;
        $this->actualBins[$bin] = new $class($bin);
      }
    }

    return $this->visibleBins;
  }

  /**
   * Set up cache overrides
   *
   * - define Heisencache\Cache as the sole cache class
   * - register pre-existing cache configurations into the Cache instance
   *   - cache_class_*
   *   - cache_class_default_class
   *
   * @return array
   *   The overridden configuration.
   */
  public function override(): array {
    $cacheConf = array_merge([
      self::VAR_CACHE_DEFAULT_CLASS => $this->overrideDefaultCacheClass(),
    ], $this->overrideCacheClasses());

    return array_merge($GLOBALS['conf'], $cacheConf);
  }

}
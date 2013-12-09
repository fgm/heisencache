Installing Heisencache
======================

Theory of operation
-------------------

Heisencache is a Drupal 7 cache plugin. As such, it is implemented within a
module package and is not a module itself. The sole role of the module is to
provide an administrative UI at some point.

Heisencache works by intercepting ALL cache settings and inserting itself as a
transparent proxy in front of the other caches, which it invokes on behalf of
the client code invoking cache operations.

Note that this plugin requires PHP >= 5.3.

Installation example
--------------------

In order to do this, the Heisencache cache plugin must be inserted into the
site settings file (`settings.php`) as the last cache setting. Typically, this
means that the bottom of `settings.php` should look like this, assuming Redis is
used as the main cache plugin.

    # Configure other caches as if Heisencache was not there.
    $conf['cache_default_class']         = 'Redis_Cache';
    $conf['cache_class_cache_form']      = 'DrupalDatabaseCache';
    $conf['cache_class_cache_update']    = 'DrupalDatabaseCache';
    $conf['cache_backends'][]            = 'sites/all/modules/contrib/redis/redis.autoload.inc';

    # Then override the configuration: declare the plugin, load it, and invoke it.
    $conf['cache_backends'][] = 'sites/all/modules/contrib/heisencache/heisencache.inc';


Configuration
-------------

The plugin can be configured per-site by implementing a `settings.heisencache.inc`
file in the site settings directory. Copy `default.settings.heisencache.inc` to
the site settings directory, and edit it to add configuration of your choice,
following the examples in that file.

Your configuration will most of the time involve:

  * registering a number of event Subscribers, like MissSubscriber
  * registering one WriterSubscriber, passing it the events emitted by all
    the subscribers you registered previously, plus the `onShutdown` event to
    trigger writes only once per page, on shutdown.
  * supplied WriterSubscriber classes:
    * The `WatchdogWriterSubscriber` does not need the Heisencache module, but
      cannot log cache events on cached pages.
    * The `SqlWriterSubscriber` can log events on cached pages, but needs the
      Heisencache module to be enabled.

Running tests
-------------

The plugin is testable with PHPUnit. If you installed it in
`sites/all/modules/contrib/heisencache`, you do not need to modify the PHPunit
configuration file and you can run tests directly from the module base
directory. Otherwise:

- copy `phpunit.xml.dist` to `phpunit.xml`
- modify the `HEISENCACHE_DRUPAL_BASE` value to point to the Drupal base directory.
- run PHPunit like: `phpunit src`

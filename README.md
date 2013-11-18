Installing Heisencache
======================

Theory of operation
-------------------

Heisencache is a Drupal 7 cache plugin. As such, it is implemented within a
module package and is not a module itself. The sole role of the module is to
provide an administrative UI.

Heisencache works by intercepting ALL cache settings and inserting itself as a
transparent proxy in front of the other caches, which it invokes on behalf of
the client code invoking cache operations.

Configuration example
---------------------

In order to do this, the Heisencache cache plugin must be inserted into the
site settings file (settings.php) as the last cache setting. Typicall, this
means settings should look like this, assuming Redis is used as the main cache
plugin.

# Configure other caches as if Heisencache was not there.
$conf['cache_default_class']         = 'Redis_Cache';
$conf['cache_backends'][]            = 'sites/all/modules/contrib/redis/redis.autoload.inc';
$conf['cache_class_cache_form']      = 'DrupalDatabaseCache';
$conf['cache_class_cache_update']    = 'RedisPhpRedisCache';

# Then override the configuration: declare the plugin, load it, and invoke it.
$heisencache = 'sites/all/modules/contrib/heisencache/heisencache.inc';
$conf['cache_backends'][] = $heisencache;

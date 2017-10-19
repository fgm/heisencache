**Caveat emptor:** 2017-10-12 The Drupal 8 version of this project is currently 
a work in progress, with next to no usable functionality.

Heisencache
===========

Theory of operation
-------------------

Heisencache is a Drupal 8 cache plugin. Unlike the Drupal 7 version, it is an
actual module using standard Drupal kernel dispatching.

Heisencache works by intercepting ALL enabled cache backend services and 
inserting itself as a decorator in front of them. It then dispatches pre- and 
post- events around each operation on them using the standard Drupal 8 event
dispatcher (not a custom event system as in the Drupal 7 version). 

Event subscribers can then react on these events to perform any kind of 
analysis or monitoring. Heisencache comes with a number of pre-built listeners:

* Basic subscribers: 
  * `DebugSubscriber`: catch everything
  * `MissSubscriber`: catch cache misses occurring on `get[Multiple]` operations
  * `PerformanceSubscriber`: measure time and data volume for every operation
  * `WriteSubscriber`: catch only operations modifying the cache: `set[Multiple]`, 
    `invalidate[All|Multiple]`, `delete[All|Multiple]`, `removeBin`.
* Writer subscribers: these catch all calls and save the collected data during 
  shutdown
  * `SqlWriterSubscriber`: write to a raw SQL table
  * `WatchdogSubscriber`: Write using a logger channel.

Most custom extensions developed by users are likely to be custom writer 
subscribers to ship events to specific off-site storage for deferred analysis.
 
Note that this plugin requires PHP >= 7.1.


Installing
----------

Just add and enable the module: Composer will find the dependencies, and once
enabled, Heisencache will automatically find existing cache services and wrap 
around them. 

    composer require drupal/heisencache
    drush en heisencache

However, at this point, it will just make your site slower with zero benefits, 
because its logic will be added to the existing caches and the events it 
triggers will not be used by any listener.

To actually use it, you will then need to configure it, thus defining what 
event subscribers are to be used.

Configuring
-----------

The module can be configured using the `heisencache` container parameter. This
configuration contains at `subscribers` sub-key, the value for which is a map of
event names by subscriber short name.

A typical starter example would be:

```yaml
# In sites/default/services.yml:
parameters:
  # various other parameters
  heisencache:
    subscribers:
      debug: ['invalidate', 'miss']
      watchdog_writer: ~
```

Such a configuration means Heisencache is configured to instantiate:

* the `heisencache.subscriber.debug` service - implemented by the 
  `DebugSubscriber` class, and configure it to subscribe to the Heisencache 
  `invalidate` and `miss` events, by passing it these event names.
* the `heisencache.subscriber.watchdog_writer` service - implemented by the
  `WatchdogWriter` class, and let it configure the events to which it subscribes 
  on its own, by passing it no event name, not even an empty list.

<hr />

*Text below this line is only valid for the Drupal 7 version*

<hr />

Running tests
-------------

The plugin is testable with PHPUnit. If you installed it in
`sites/all/modules/contrib/heisencache`, you do not need to modify the PHPunit
configuration file and you can run tests directly from the module base
directory. Otherwise:

- copy `phpunit.xml.dist` to `phpunit.xml`
- modify the `HEISENCACHE_DRUPAL_BASE` value to point to the Drupal base directory.
- run PHPunit like: `phpunit src`

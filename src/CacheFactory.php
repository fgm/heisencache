<?php

namespace Drupal\heisencache;

use Drupal\Core\Cache\CacheFactoryInterface;
use Drupal\heisencache\Event\EventDispatcherTrait;
use Drupal\heisencache\Event\FactoryGetEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class CacheFactory is a decorator for the core CacheFactory.
 *
 * It adds event generation around the core methods, and builds decorated
 * CacheBacking instances.
 *
 * @package Drupal\heisencache
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2015-2020 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
class CacheFactory implements CacheFactoryInterface {

  use EventDispatcherTrait;

  const FACTORY_GET = 'factory get';

  /**
   * The core cache service.
   *
   * @var \Drupal\Core\Cache\CacheFactoryInterface
   */
  protected CacheFactoryInterface $coreFactory;

  /**
   * The constructor.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
   *   The event_dispatcher service.
   * @param \Drupal\Core\Cache\CacheFactoryInterface $core_factory
   *   The core cache service.
   */
  public function __construct(EventDispatcherInterface $dispatcher, CacheFactoryInterface $core_factory) {
    $this->coreFactory = $core_factory;
    $this->dispatcher = $dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public function get($bin) {
    $event = new FactoryGetEvent($bin);
    $this->dispatch($event);
    $decorated_backend = $this->coreFactory->get($bin);
    $decorator = new Cache($bin, $decorated_backend, $this->dispatcher);
    $this->dispatch($event->setPost());
    return $decorator;
  }

}

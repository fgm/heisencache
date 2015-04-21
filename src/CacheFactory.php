<?php
/**
 * @file
 * CacheFactory.php
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2015 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace Drupal\heisencache;


use Drupal\Core\Cache\CacheFactoryInterface;
use Drupal\heisencache\Event\EventDispatcherTrait;
use Drupal\heisencache\Event\FactoryGetEvent;
use OSInet\Heisencache\Cache;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class CacheFactory is a decorator for the core CacheFactory, adding event
 * generation around its methods, and building decorated CacheBacking instances.
 *
 * @package Drupal\heisencache
 */
class CacheFactory implements CacheFactoryInterface {

  use EventDispatcherTrait;

  const FACTORY_GET = 'factory get';

  /**
   * @var \Drupal\Core\Cache\CacheFactoryInterface
   */
  protected $decorated_factory;

  public function __construct(EventDispatcherInterface $dispatcher, CacheFactoryInterface $core_factory)  {
    $this->decorated_factory = $core_factory;
    $this->dispatcher = $dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public function get($bin) {
    $event = new FactoryGetEvent($bin);
    $this->dispatch($event);
    $decorated_backend = $this->decorated_factory->get($bin);
    $decorator = new Cache($bin, $decorated_backend, $this->dispatcher);
    $this->dispatch($event->setPost());
    return $decorator;
  }

}

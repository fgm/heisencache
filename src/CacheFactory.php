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
use Drupal\heisencache\Event\FactoryGetEvent;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CacheFactory implements CacheFactoryInterface, ContainerAwareInterface {

  const GET_BIN_PRE = 'get_bin_pre';
  const GET_BIN_POST = 'get_bin_post';

  use ContainerAwareTrait;

  /**
   * @var \Drupal\Core\Cache\CacheFactoryInterface
   */
  protected $core_factory;

  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  public function __construct(EventDispatcherInterface $dispatcher, CacheFactoryInterface $core_factory)  {
    $this->core_factory = $core_factory;
    $this->dispatcher = $dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public function get($bin) {
    $event = new FactoryGetEvent($bin);
    $this->dispatcher->dispatch(static::GET_BIN_PRE, $event);
    $ret = $this->core_factory->get($bin);
    $this->dispatcher->dispatch(static::GET_BIN_POST, $event);
    return $ret;
  }

}

<?php

namespace Drupal\heisencache\EventSubscriber;

use Drupal\heisencache\Event\EventBase;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Trait EventSourceTrait allows services to identify as event sources.
 *
 * As such, they are tagged with EventSourceInterface::EMITTER_TAG, and can
 * implement DescribedServiceInterface with zero code.
 *
 * @package Drupal\heisencache\EventSubscriber
 */
trait EventSourceTrait {

  protected static $emittedEvents;

  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Support DescribedServiceInterface.
   *
   * @return \Symfony\Component\DependencyInjection\Definition
   *
   * @see \Drupal\heisencache\DescribedServiceInterface::describe()
   */
  public static function describe(): Definition {
    $def = parent::describe();
    $def->addTag(EventSourceInterface::EMITTER_TAG);
    return $def;
  }

  /**
   * {@inheritdoc}
   */
  public static function getEmittedEvents() {
    return static::$emittedEvents ?? [];
  }

  public function dispatcher() {
    if (!isset($this->eventDispatcher)) {
      $this->eventDispatcher = \Drupal::service('event_dispatcher');
    }

    return $this->eventDispatcher;
  }

  public function dispatch(EventBase $event) {
    $this->dispatcher()->dispatch($event->name(), $event);
  }

}

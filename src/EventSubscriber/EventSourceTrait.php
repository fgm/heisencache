<?php

namespace Drupal\heisencache\EventSubscriber;

use Drupal\heisencache\Event\EventBase;
use Symfony\Component\DependencyInjection\Definition;

trait EventSourceTrait {

  protected static $emittedEvents;

  /**
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

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

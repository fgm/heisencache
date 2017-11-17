<?php

namespace Drupal\heisencache\EventSubscriber;

use Drupal\heisencache\Event\EventInterface;
use Drupal\heisencache\HeisencacheServiceProvider as H;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * WatchdogWriterSubscriber class: accumulate events, write them at end of page.
 *
 * @package Drupal\heisencache\EventSubscriber
 */
class WatchdogWriterSubscriber extends WriterBase {

  /**
   * The logger.channel.heisencache service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * WatchdogWriter constructor.
   *
   * @param array|null $events
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct($events = [], LoggerInterface $logger = NULL) {
    parent::__construct($events);
    $this->logger = $logger;
  }

  public static function describe(): Definition {
    $def = parent::describe()
      ->addArgument(new Reference(H::LOGGER))
    ;
    $def->replaceArgument(0, [
      KernelEvents::TERMINATE,
      EventInterface::IN . EventInterface::BACKEND_CONSTRUCT,
    ]);
    return $def;
  }

  public function onKernelTerminate(): void {
    if (TRUE || !empty($this->history)) {
      $this->logger->debug('Cache events: @events', [
        '@events' => serialize($this->history),
      ]);
    }
  }
}

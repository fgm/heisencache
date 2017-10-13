<?php

namespace Drupal\heisencache\EventSubscriber;


use Drupal\Core\Logger\RfcLogLevel;

/**
 * WatchdogWriterSubscriber class: accumulate events, write them at end of page.
 *
 * @package Drupal\heisencache\EventSubscriber
 */
class WatchdogWriter extends BaseWriter {

  public function onShutdown(): void {
    if (!empty($this->history)) {
      watchdog('heisencache', 'Cache events: @events', array(
        '@events' => serialize($this->history),
      ), RfcLogLevel::DEBUG);
    }
  }
}

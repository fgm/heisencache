<?php

declare(strict_types=1);

namespace Drupal\heisencache\EventSubscriber;


use Psr\Log\LogLevel;

/**
 * Watchdog Writer: accumulate events, write them to dblog at end of page.
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
class WatchdogWriterSubscriber extends BaseWriterSubscriber {

  public function onShutdown(string $_channel): void {
    if (!empty($this->history)) {
      watchdog('heisencache', 'Cache events: @events', array(
        '@events' => serialize($this->history),
      ), WATCHDOG_DEBUG);
    }
  }
}

<?php

declare(strict_types=1);

namespace Drupal\heisencache\EventSubscriber;

/**
 * A writer subscriber writing to a table in a SQL DBMS.
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

class SqlWriterSubscriber extends BaseWriterSubscriber {
  const SINK = 'heisencache_sink';

  /**
   * @param string[] $events
   */
  public function __construct($events = array()) {
    parent::__construct($events);
  }

  /**
   * @throws \RuntimeException
   */
  public function ensureDestinationTable() {
    if (!db_table_exists(static::SINK)) {
      throw new \RuntimeException(strtr('Missing SQL sink table @sink.', array(
        '@sink' => static::SINK,
      )));
    }
  }

  public function onShutdown() {
    if (empty($this->history)) {
      return;
    }

    // This runs as a shutdown function, so we cannot just let an exception bubble.
    try {
      $this->ensureDestinationTable();
      $record = array(
        'uid' => $GLOBALS['user']->uid ?? 0,
        'data' => serialize($this->history),
      );
      drupal_write_record(static::SINK, $record);
    }
    catch (\RuntimeException $e) {
      echo "<p>" . $e->getMessage() . "</p>"
        . "<p>The SqlWriterSubscriber needs the Heisencache module to be enabled.</p>";
    }

  }
}

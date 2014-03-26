<?php
/**
 * @file
 *   SqlSubscriber.php
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache;


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
        'uid' => isset($GLOBALS['user']->uid) ? $GLOBALS['user']->uid : 0,
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
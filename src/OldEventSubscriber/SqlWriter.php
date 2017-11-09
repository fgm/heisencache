<?php

namespace Drupal\heisencache\EventSubscriber;

use Drupal\Core\Database\Connection;
use Drupal\heisencache\Exception\RuntimeException;

class SqlWriter extends BaseWriter {
  const SINK = 'heisencache_sink';

  /**
   * The database service.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * SqlWriterSubscriber constructor.
   *
   * @param array $events
   *   The name of the events to record.
   * @param \Drupal\Core\Database\Connection $database
   *   The database service.
   */
  public function __construct(array $events = [], Connection $database) {
    parent::__construct($events);
    $this->database = $database;
  }

  /**
   * @throws \RuntimeException
   */
  public function ensureDestinationTable() {
    if (!$this->database->schema()->tableExists(static::SINK)) {
      throw new RuntimeException(strtr('Missing SQL sink table @sink.', array(
        '@sink' => static::SINK,
      )));
    }
  }

  public function onTerminate(): void {
    if (empty($this->history)) {
      return;
    }

    // Runs as a shutdown function, so we cannot just let an exception bubble.
    try {
      $this->ensureDestinationTable();
      $record = array(
        'uid' => $GLOBALS['user']->uid ?? 0,
        'data' => serialize($this->history),
      );
      drupal_write_record(static::SINK, $record);
    }
    catch (RuntimeException $e) {
      echo "<p>" . $e->getMessage() . "</p>"
        . "<p>The SqlWriterSubscriber needs the Heisencache module to be enabled.</p>";
    }
  }

}

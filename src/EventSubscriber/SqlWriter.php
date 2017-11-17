<?php

namespace Drupal\heisencache\EventSubscriber;

use Drupal\Core\Database\Connection;
use Drupal\heisencache\Exception\RuntimeException;

class SqlWriter extends WriterBase {
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

  public function onKernelTerminate(): void {
    if (empty($this->history)) {
      return;
    }

    // May run as a shutdown function, so we cannot just let an exception bubble.
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

  public static function hookSchema(array $schema) {
    $schema[self::SINK] = array(
      'description' => 'Stores raw Heisencache events par page cycle, not meant for direct consumption.',
      'fields' => array(
        'id' => array(
          'type' => 'serial',
          'unsigned' => TRUE,
          'not null' => TRUE,
          'description' => "The page cycle id",
        ),
        'uid' => array(
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => TRUE,
          'default' => 0,
        ),
        'data' => array(
          'type' => 'text',
          // On MySQL, medium text only holds up to 16 MB.
          // Some configurations may write more than this.
          'size' => 'big',
          'not null' => TRUE,
          'description' => 'The event data in bulk, as observed by subscribers',
        ),
      ),
      'primary key' => array('id'),
    );

    return $schema;
  }

}

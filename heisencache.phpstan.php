<?php

const DRUPAL_ROOT = __DIR__ . "/../../../../..";

require_once DRUPAL_ROOT . "/includes/bootstrap.inc";
require_once DRUPAL_ROOT . "/includes/cache.inc";
require_once DRUPAL_ROOT . "/sites/default/default.settings.php";

$GLOBALS['conf'] = [];
$_SERVER['HTTP_HOST'] = 'localhost'; // For conf_path().

/**
 * Checks if a table exists.
 *
 * @param $table
 *   The name of the table in drupal (no prefixing).
 *
 * @return
 *   TRUE if the given table exists, otherwise FALSE.
 */
function db_table_exists($table): bool {
  return TRUE;
}

/**
 * @param string $table
 * @param array|object $record
 * @param array $primary_keys
 *
 * @return bool|int
 */
function drupal_write_record(
  string $table,
  &$record,
  array $primary_keys = []
): mixed {
  return 1;
}

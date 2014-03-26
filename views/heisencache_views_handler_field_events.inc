<?php

/**
 * @file
 * Heisencache: field handler for bulk data in watchdog.
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

class heisencache_views_handler_field_events extends views_handler_field {

  /**
   * Render the field.
   *
   * FIXME: sanitize !
   *
   * @param $values
   *   The values retrieved from the database.
   *
   * @return string
   */
  function render($values) {
    $value = $this->get_value($values);
    // return $this->sanitize_value($value);

    return '<pre>' . print_r($value, TRUE) . '</pre>';
  }

  /**
   * Unserialize values prior to rendering.
   */
  function pre_render(&$values) {
    foreach ($values as &$value) {
      $raw_value = $this->get_value($value);
      try {
        $wrapper = unserialize($raw_value);
        $events = $wrapper['@events'];
        $history = unserialize($events);
      }
      catch (Exception $e) {
        $history = $raw_value;
      }
      $value->{$this->field_alias} = $history;
    }
  }
}
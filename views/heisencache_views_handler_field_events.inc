<?php

/**
 * @file
 * Heisencache: field handler for bulk data in watchdog.
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

declare(strict_types=1);

/**
 * Views field handler for Heisencache Event data.
 */
class heisencache_views_handler_field_events extends views_handler_field {

  /**
   * Render the field.
   *
   * FIXME: sanitize !
   *
   * @param array $values
   *   The values retrieved from the database.
   *
   * @return string
   *   The string representation of the field.
   */
  public function render($values) {
    $value = $this->get_value($values);
    return "<pre>" . $this->sanitize_value($value) . "</pre>";
  }

  /**
   * Unserialize values prior to rendering.
   */
  public function pre_render(&$values) {
    foreach ($values as &$value) {
      /** @var mixed $raw_value */
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

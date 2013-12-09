<?php

class heisencache_views_handler_field_events extends views_handler_field {

  /**
   * Render the field.
   *
   * FIXME: sanitize !
   *
   * @param $values
   *   The values retrieved from the database.
   */
  function render($values) {
    $value = $this->get_value($values);
    // return $this->sanitize_value($value);

    $rows = array();
    return '<pre>' . print_r($value, TRUE) . '</pre>';
  }

  /**
   * Deserialize values prior to rendering.
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
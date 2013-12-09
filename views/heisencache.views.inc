<?php

function _heisencache_views_data_watchdog() {
  $ret = array();
  $ret['watchdog'] = array(
    // Base table information.
    'table' => array(
      'group' => 'Heisencache/dblog',
      'base' => array(
        'field' => 'wid',
        'title' => t('Watchdog'),
        'help' => t('Watchdog is the table providing the Database Logging storage'),
        'weight' => 0,
      ),
    ),

    // Watchdog id.
    'wid' => array(
      'title' => t('Wid'),
      'help' => t('The id of the watchdog row'),
      'field' => array(
        'handler' => 'views_handler_field_numeric',
        'click  sortable' => TRUE,
      ),
    ),

    // Watchdog user id.
    'uid' => array(
      'title' => t('Emitter uid'),
      'help' => t('The user for whom the content is being generated.'),
      'relationship' => array(
        'title' => t('Emitter'),
        'help' => t('Relate events to the user who caused them.'),
        'handler' => 'views_handler_relationship',
        'field' => 'uid',
        'base' => 'users',
        'base field' => 'uid',
        'label' => t('emitter'),
      ),
      'filter' => array(
        'handler' => 'views_handler_filter_user_name',
      ),
      'argument' => array(
        'handler' => 'views_handler_argument_numeric',
      ),
      'field' => array(
        'handler' => 'views_handler_field_user',
        'click  sortable' => TRUE,
      ),
    ),

    // Watchdog type.
    'type' => array(
      'title' => t('Type'), // The item it appears as on the UI,
      'help' => t('The watchdog type ("heisencache", etc).'), // The help that appears on the UI,
      'field' => array(
        'handler' => 'views_handler_field',
        'click sortable' => TRUE,
      ),
      'sort' => array(
        'handler' => 'views_handler_sort',
      ),
      'filter' => array(
        'handler' => 'views_handler_filter_string',
      ),
    ),

    'variables' => array(
      'title' => t('Events'),
      'help' => t('All the events logged on that page cycle'),
      'field' => array(
        'handler' => 'heisencache_views_handler_field_events',
      ),
    ),
  );

  return $ret;
}

function heisencache_views_data_alter(&$data) {
  $data = array();
  if (module_exists('dblog')) {
    $data = array_merge($data, _heisencache_views_data_watchdog());
  }

}

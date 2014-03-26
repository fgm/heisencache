<?php

/**
 * @file
 * Heisencache Views integration.
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

use OSInet\Heisencache\SqlWriterSubscriber;

/**
 * Helper for hook_views_data_alter() on the Heisencache sink table.
 *
 * @return array
 */
function _heisencache_views_data_sink() {
  $ret = array();
  $ret[SqlWriterSubscriber::SINK] = array(
    'table' => array(
      'group' => 'Heisencache/sql',
      'base' => array(
        'field' => 'id',
        'title' => t('Heisencache Sink'),
        'help' => t('The Heisencache sink is a temporary space where bulk data is sent at the end of a page cycle.'),
        'weight' => 0,
      ),
    ),

    'id' => array(
      'title' => t('Id'),
      'help' => t('The id of the sink row'),
      'sort' => array(
        'handler' => 'views_handler_sort',
      ),
      'field' => array(
        'handler' => 'views_handler_field_numeric',
        'click  sortable' => TRUE,
      ),
    ),

    // Sink user id.
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
      'sort' => array(
        'handler' => 'views_handler_sort',
      ),
      'argument' => array(
        'handler' => 'views_handler_argument_numeric',
      ),
      'field' => array(
        'handler' => 'views_handler_field_user',
        'click  sortable' => TRUE,
      ),
    ),

    'data' => array(
      'title' => t('Serialized event data'),
      'help' => t('A serialized array of all the events observed by the SQL Writer Subscriber and not yet processed.'),
      'field' => array(
        'handler' => 'views_handler_field',
      ),
    ),
  );
  return $ret;
}

/**
 * Helper for hook_views_data_alter() on {watchdog} table.
 *
 * @return array
 */
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
      'join' => array(
        'users' => array(
          'left field' => 'uid',
          'field' => 'uid',
        ),
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
  $data = array_merge($data, _heisencache_views_data_watchdog());
  $data = array_merge($data, _heisencache_views_data_sink());
}

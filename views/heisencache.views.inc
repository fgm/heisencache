<?php

/**
 * @file
 * Heisencache Views integration.
 *
 * @copyright (c) 2013-2021 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

declare(strict_types=1);

use Drupal\heisencache\EventSubscriber\SqlWriterSubscriber;

/**
 * Helper for hook_views_data_alter() on the Heisencache sink table.
 *
 * @return array
 *   Views integration updates.
 */
function _heisencache_views_data_sink() {
  $ret = [];
  $ret[SqlWriterSubscriber::SINK] = [
    'table' => [
      'group' => 'Heisencache/sql',
      'base' => [
        'field' => 'id',
        'title' => t('Heisencache Sink'),
        'help' => t('The Heisencache sink is a temporary space where bulk data is sent at the end of a page cycle.'),
        'weight' => 0,
      ],
    ],

    'id' => [
      'title' => t('Id'),
      'help' => t('The id of the sink row'),
      'sort' => [
        'handler' => 'views_handler_sort',
      ],
      'field' => [
        'handler' => 'views_handler_field_numeric',
        'click  sortable' => TRUE,
      ],
    ],

    // Sink user id.
    'uid' => [
      'title' => t('Emitter uid'),
      'help' => t('The user for whom the content is being generated.'),
      'relationship' => [
        'title' => t('Emitter'),
        'help' => t('Relate events to the user who caused them.'),
        'handler' => 'views_handler_relationship',
        'field' => 'uid',
        'base' => 'users',
        'base field' => 'uid',
        'label' => t('emitter'),
      ],
      'filter' => [
        'handler' => 'views_handler_filter_user_name',
      ],
      'sort' => [
        'handler' => 'views_handler_sort',
      ],
      'argument' => [
        'handler' => 'views_handler_argument_numeric',
      ],
      'field' => [
        'handler' => 'views_handler_field_user',
        'click  sortable' => TRUE,
      ],
    ],

    'data' => [
      'title' => t('Serialized event data'),
      'help' => t('A serialized array of all the events observed by the SQL Writer Subscriber and not yet processed.'),
      'field' => [
        'handler' => 'views_handler_field',
      ],
    ],
  ];
  return $ret;
}

/**
 * Helper for hook_views_data_alter() on {watchdog} table.
 *
 * @return array
 *   Table views integration updates.
 */
function _heisencache_views_data_watchdog() {
  $ret = [];
  $ret['watchdog'] = [
    // Base table information.
    'table' => [
      'group' => 'Heisencache/dblog',
      'base' => [
        'field' => 'wid',
        'title' => t('Watchdog'),
        'help' => t('Watchdog is the table providing the Database Logging storage'),
        'weight' => 0,
      ],
      'join' => [
        'users' => [
          'left field' => 'uid',
          'field' => 'uid',
        ],
      ],
    ],

    // Watchdog id.
    'wid' => [
      'title' => t('Wid'),
      'help' => t('The id of the watchdog row'),
      'field' => [
        'handler' => 'views_handler_field_numeric',
        'click  sortable' => TRUE,
      ],
    ],

    // Watchdog user id.
    'uid' => [
      'title' => t('Emitter uid'),
      'help' => t('The user for whom the content is being generated.'),
      'relationship' => [
        'title' => t('Emitter'),
        'help' => t('Relate events to the user who caused them.'),
        'handler' => 'views_handler_relationship',
        'field' => 'uid',
        'base' => 'users',
        'base field' => 'uid',
        'label' => t('emitter'),
      ],
      'filter' => [
        'handler' => 'views_handler_filter_user_name',
      ],
      'argument' => [
        'handler' => 'views_handler_argument_numeric',
      ],
      'field' => [
        'handler' => 'views_handler_field_user',
        'click  sortable' => TRUE,
      ],
    ],

    // Watchdog type.
    'type' => [
      // The item it appears as on the UI.
      'title' => t('Type'),
      // The help that appears on the UI.
      'help' => t('The watchdog type ("heisencache", etc).'),
      'field' => [
        'handler' => 'views_handler_field',
        'click sortable' => TRUE,
      ],
      'sort' => [
        'handler' => 'views_handler_sort',
      ],
      'filter' => [
        'handler' => 'views_handler_filter_string',
      ],
    ],

    'variables' => [
      'title' => t('Events'),
      'help' => t('All the events logged on that page cycle'),
      'field' => [
        'handler' => 'heisencache_views_handler_field_events',
      ],
    ],
  ];

  return $ret;
}

/**
 * Implements hook_views_data_alter().
 */
function heisencache_views_data_alter(&$data) {
  $data = array_merge($data, _heisencache_views_data_watchdog());
  $data = array_merge($data, _heisencache_views_data_sink());
}

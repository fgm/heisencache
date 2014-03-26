<?php
/**
 * @file
 * Heisencache: exported Watchdog view.
 *
 * @author: Frederic G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2013-2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

$view = new view();
$view->name = 'heisencache_watchdog';
$view->description = 'View the bulk data stored by the Watchdog Writer Subscriber and the dblog module before processing.';
$view->tag = 'Heisencache';
$view->base_table = 'watchdog';
$view->human_name = 'Heisencache Watchdog';
$view->core = 7;
$view->api_version = '3.0';
$view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

/* Display: Master */
$handler = $view->new_display('default', 'Master', 'default');
$handler->display->display_options['title'] = 'Heisencache: Bulk Watchdog';
$handler->display->display_options['use_more_always'] = FALSE;
$handler->display->display_options['access']['type'] = 'perm';
$handler->display->display_options['access']['perm'] = 'administer site configuration';
$handler->display->display_options['cache']['type'] = 'none';
$handler->display->display_options['query']['type'] = 'views_query';
$handler->display->display_options['exposed_form']['type'] = 'basic';
$handler->display->display_options['pager']['type'] = 'full';
$handler->display->display_options['pager']['options']['items_per_page'] = '1';
$handler->display->display_options['pager']['options']['offset'] = '0';
$handler->display->display_options['pager']['options']['id'] = '0';
$handler->display->display_options['pager']['options']['quantity'] = '9';
$handler->display->display_options['style_plugin'] = 'table';
$handler->display->display_options['style_options']['columns'] = array(
  'wid' => 'wid',
  'uid' => 'uid',
);
$handler->display->display_options['style_options']['default'] = '-1';
$handler->display->display_options['style_options']['info'] = array(
  'wid' => array(
    'align' => '',
    'separator' => '',
    'empty_column' => 0,
  ),
  'uid' => array(
    'align' => '',
    'separator' => '',
    'empty_column' => 0,
  ),
);
/* Relationship: Heisencache/dblog: Emitter */
$handler->display->display_options['relationships']['uid']['id'] = 'uid';
$handler->display->display_options['relationships']['uid']['table'] = 'watchdog';
$handler->display->display_options['relationships']['uid']['field'] = 'uid';
/* Field: Heisencache/dblog: Wid */
$handler->display->display_options['fields']['wid']['id'] = 'wid';
$handler->display->display_options['fields']['wid']['table'] = 'watchdog';
$handler->display->display_options['fields']['wid']['field'] = 'wid';
/* Field: Heisencache/dblog: Emitter uid */
$handler->display->display_options['fields']['uid']['id'] = 'uid';
$handler->display->display_options['fields']['uid']['table'] = 'watchdog';
$handler->display->display_options['fields']['uid']['field'] = 'uid';
$handler->display->display_options['fields']['uid']['label'] = 'Emitter';
/* Field: Heisencache/dblog: Events */
$handler->display->display_options['fields']['variables']['id'] = 'variables';
$handler->display->display_options['fields']['variables']['table'] = 'watchdog';
$handler->display->display_options['fields']['variables']['field'] = 'variables';
/* Filter criterion: Heisencache/dblog: Type */
$handler->display->display_options['filters']['type']['id'] = 'type';
$handler->display->display_options['filters']['type']['table'] = 'watchdog';
$handler->display->display_options['filters']['type']['field'] = 'type';
$handler->display->display_options['filters']['type']['value'] = 'heisencache';

/* Display: Page */
$handler = $view->new_display('page', 'Page', 'page');
$handler->display->display_options['path'] = 'admin/reports/dblog/heisencache';
$handler->display->display_options['menu']['type'] = 'tab';
$handler->display->display_options['menu']['title'] = 'Heisencache database log';
$handler->display->display_options['menu']['weight'] = '0';
$handler->display->display_options['menu']['context'] = 0;
$handler->display->display_options['menu']['context_only_inline'] = 0;

<?php
/**
 * @file
 * Defines the configuration of the Heisencache cache proxy:
 * - instantiate subscribers
 *
 * Copy this file to the site settings directory for Heisencache to pick it up,
 * and edit it to fit your needs.
 *
 * @author: marand
 *
 * @copyright (c) 2013 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

// Operate in the plugin namespace
namespace OSInet\Heisencache;

// Obtain the plugin configuration instance.
$config = Config::instance();

$config->getEmitter()
//  ->register(new DebugSubscriber(array('beforeGetMultiple', 'onCacheConstruct')))
//  ->register(new MissSubscriber())
  ->register(new WatchdogWriterSubscriber());

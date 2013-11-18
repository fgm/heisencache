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

// Most configurations will want to add new subscribers. Some are provided
// in the plugin directory.
$srcDir = $config->getSrcDir();

// Include them: the Drupal autoloader is not available at this point.

// require_once "$srcDir/DebugSubscriber.php";
// $debug = new DebugSubscriber(array('beforeGetMultiple', 'onCacheConstruct'));
// $config->getEmitter()->register($debug);

require_once "$srcDir/MissSubscriber.php";
$config->getEmitter()->register(new MissSubscriber());
<?php
/**
 * @file
 *   EventListener.php
 *
 * @author: marand
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Heisencache;


abstract class BaseEventSubscriber implements EventSubscriberInterface {
  /**
   * @var string
   */
  protected $name;

  public function __construct($name) {
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

}

<?php

namespace Drupal\heisencache;

use Symfony\Component\DependencyInjection\Definition;

/**
 * An interface for classes providing a Symfony-compatible service description.
 *
 * @package Drupal\heisencache
 */
interface DescribedServiceInterface {

  /**
   * Return a default definition for the service implemented by the class.
   *
   * @return \Symfony\Component\DependencyInjection\Definition
   *   The definition for the service implemented by the self-described class.
   */
  public static function describe() : Definition;

}

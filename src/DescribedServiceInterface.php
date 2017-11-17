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
   * @param array $knownEvents
   *   The names of the events known to the system. Useful for wildcards.
   * @return \Symfony\Component\DependencyInjection\Definition The definition for the service implemented by the self-described class.
   *   The definition for the service implemented by the self-described class.
   */
  public static function describe(array $knownEvents = []) : Definition;

}

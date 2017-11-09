<?php

namespace Drupal\heisencache\EventSubscriber;

interface TerminateWriterInterface {

  /**
   * Writes the collected information gathered during the page cycle.
   */
  public function onTerminate(): void;

}

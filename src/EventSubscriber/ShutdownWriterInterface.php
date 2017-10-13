<?php

namespace Drupal\heisencache\EventSubscriber;

interface ShutdownWriterInterface {

  /**
   * Writes the collected information gathered during the page cycle.
   */
  public function onShutdown(): void;

}

<?php

namespace Drupal\heisencache;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use League\CommonMark\Converter;

/**
 * Class Requirements implements hook_requirements().
 *
 * @package Drupal\heisencache
 */
class Requirements {

  use StringTranslationTrait;

  /**
   * Implements hook_requirements().
   */
  public function hookRequirements($phase) {
    $req = [];
    $this->requireCommonMark($req);
    return $req;
  }

  /**
   * Validate the CommonMark requirement.
   *
   * @param array $req
   *   The current state of the requirements list.
   */
  protected function requireCommonMark(array &$req) {
    if (class_exists(Converter::class)) {
      return;
    }

    $req[__METHOD__] = [
      'title' => $this->t('Heisencache help'),
      'description' => $this->t('Could not find the League/CommonMark library. Did you add it to the composer.json of the site and run composer install?'),
      'severity' => REQUIREMENT_ERROR,
    ];
  }

}

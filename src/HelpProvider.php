<?php

namespace Drupal\heisencache;

use League\CommonMark\CommonMarkConverter;

/**
 * Class HelpProvider implements hook_help().
 */
class HelpProvider {
  const README = __DIR__ . '/../README.md';

  /**
   * Implements hook_help().
   */
  public function help(string $routeName) : string {
    switch ($routeName) {
      case 'help.page.heisencache':
        return $this->helpPage();

      default:
        return '';
    }
  }

  /**
   * Provide the help page.
   *
   * @return string
   *   The rendered help, as HTML.
   */
  protected function helpPage() : string {
    // Hook_requirements() will have prevented module enabling if the library
    // was not already present, so there is no need to check for its presence.
    $converter = new CommonMarkConverter();
    $markdown = file_get_contents(self::README);
    $html = $converter->convertToHtml($markdown);
    return $html;
  }

}

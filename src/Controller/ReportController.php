<?php

namespace Drupal\heisencache\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\heisencache\Event\EventBase;
use Drupal\heisencache\EventSubscriber\ConfigurableListenerBase;
use Drupal\heisencache\EventSubscriber\EventSourceInterface;
use Drupal\heisencache\HeisencacheServiceProvider as H;
use Robo\Task\Vcs\loadShortcuts;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ReportController.
 *
 * @package Drupal\heisencache\Controller
 */
class ReportController implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * @var array
   */
  protected $listeners;

  /**
   * @var array
   */
  protected $params;

  public function __construct(array $params, array $listeners) {
    $this->params = $params;
    $this->listeners = $listeners;
  }

  public static function create(ContainerInterface $container) {
    $params = $container->getParameter(H::MODULE);
    $listeners = $params['subscribers'];
    array_walk($listeners, function (&$listener, $name) use ($container) {
      $listener = $container->get(H::MODULE . ".subscriber.${name}", $name);
    });
    return new static($params, $listeners);
  }

  public function build(): array {
    $rows = [];
    ksm($this->listeners);
    foreach ($this->params['subscribers'] as $subscriber => $events) {
      $row = [];
      $row[] = $subscriber;
      $cell = ($events === NULL)
        ? $this->t('Built-in selection')
        : [
          'data' => [
            '#theme' => 'item_list',
            '#items' => (array) $events,
          ],
        ];
      $row[] = $cell;

      $listener = $this->listeners[$subscriber];
      $cell = $listener instanceof EventSourceInterface
        ? [
          'data' => [
            '#theme' => 'item_list',
            '#items' => $listener::getEmittedEvents(),
            ]
          ]
        : $this->t('None');
      $row[] = $cell;

      $rows[] = $row;
    }

    $build['subscribers'] = [
      '#caption' => $this->t('Event subscriptions'),
      '#theme' => 'table',
      '#header' => [
        $this->t('Subscriber'),
        $this->t('Listened events'),
        $this->t('Emitted events'),
      ],
      '#rows' => $rows,
    ];

    return $build;
  }

}

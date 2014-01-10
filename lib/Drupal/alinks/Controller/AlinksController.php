<?php
namespace Drupal\alinks\Controller;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\alinks\AlinksManager;
  class AlinksController implements ContainerInjectionInterface {
  protected $alinksManager;

  /**
  * This method lets us inject the services this class needs.
  *
  * Only inject services that are actually needed. Which services
  * are needed will vary by the controller.
  */
  public static function create(ContainerInterface $container) {

    return new static($container->get('alinks.manager'));
  }
  public function __construct(AlinksManager $alinksManager) {
    $this->alinksManager = $alinksManager;
  }

  /**
  * This is the method that will get called, with the services above already available.
  */
  public function adminOverview() {
  // ...
  }
}

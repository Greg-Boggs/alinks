<?php

namespace Drupal\alinks\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Returns responses for alinks configuration routes.
 */
class AlinksController implements ContainerInjectionInterface {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new controller.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration factory.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * Remove the given entity type, bundle and display from alinks configuration.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Return a redirect to the alinks settings page.
   */
  public function delete($entity_type, $entity_bundle, $entity_display) {
    $config = $this->configFactory->getEditable('alinks.settings');
    $displays = array_values(array_filter($config->get('displays'), function ($display) use ($entity_type, $entity_bundle, $entity_display) {
      return !($display['entity_type'] == $entity_type && $display['entity_bundle'] == $entity_bundle && $display['entity_display'] == $entity_display);
    }));
    $config->set('displays', $displays)->save();
    return new RedirectResponse(Url::fromRoute('alink_keyword.settings')->setAbsolute()->toString());
  }

}

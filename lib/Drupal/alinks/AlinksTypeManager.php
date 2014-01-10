<?php

/**
 * @file
 * Contains \Drupal\alinks\AlinksTypeManager.
 */

namespace Drupal\alinks;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Manages pants type plugins.
 */
class AlinksTypeManager extends DefaultPluginManager {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, LanguageManager $language_manager, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/AlinksType', $namespaces, 'Drupal\alinks\Annotation\AlinksType');

    $this->alterInfo($module_handler, 'alinks_type_info');
    $this->setCacheBackend($cache_backend, $language_manager, 'alinks_type');
  }

}

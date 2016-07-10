<?php

namespace Drupal\alinks\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Alink entities.
 */
interface AlinkInterface extends ConfigEntityInterface {
  public function getStartBoundary();
  public function getText();
  public function getEndBoundary();
  public function getCaseInsensitive();
  public function getUrl();
  public function getUrlTitle();
  public function getExternal();
  public function getClass();
  public function getWeight();
}

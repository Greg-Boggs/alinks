<?php

/**
 * @file
 * Contains \Drupal\alinks\AlinksTypeInterface.
 */

namespace Drupal\alinks;

/**
 * Defines the interface for alinks types.
 */
interface AlinksTypeInterface {

  /**
   * Returns a render array to display when the alinks are on.
   *
   * @return array
   *   A render array.
   */
  public function viewAlinksOn();

  /**
   * Returns a render array to display when the alinks are off.
   *
   * @return array
   *   A render array.
   */
  public function viewAlinksOff();

}

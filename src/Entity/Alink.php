<?php

namespace Drupal\alinks\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Defines the Alink entity.
 *
 * @ConfigEntityType(
 *   id = "alink",
 *   label = @Translation("Alink"),
 *   handlers = {
 *     "list_builder" = "Drupal\alinks\AlinkListBuilder",
 *     "form" = {
 *       "add" = "Drupal\alinks\Form\AlinkForm",
 *       "edit" = "Drupal\alinks\Form\AlinkForm",
 *       "delete" = "Drupal\alinks\Form\AlinkDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\alinks\AlinkHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "alink",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/alink/{alink}",
 *     "add-form" = "/admin/structure/alink/add",
 *     "edit-form" = "/admin/structure/alink/{alink}/edit",
 *     "delete-form" = "/admin/structure/alink/{alink}/delete",
 *     "collection" = "/admin/structure/alink"
 *   }
 * )
 */
class Alink extends ConfigEntityBase implements ConfigEntityInterface {

  /**
   * The ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label.
   *
   * @var string
   */
  public $label;

  /**
   * The start boundary.
   *
   * @var int
   */
  public $start_boundary;

  /**
   * The link text.
   *
   * @var string
   */
  public $text;

  /**
   * The end boundary.
   *
   * @var int
   */
  public $end_boundary;

  /**
   * Case insensitive matching.
   *
   * @var int
   */
  public $case_insensitive;

  /**
   * The link url.
   *
   * @var string
   */
  public $url;

  /**
   * The link title.
   *
   * @var string
   */
  public $url_title;

  /**
   * Is external link.
   *
   * @var int
   */
  public $external;

  /**
   * The link class.
   *
   * @var string
   */
  public $class;

  /**
   * The link weight.
   *
   * @var int
   */
  public $weight;
}

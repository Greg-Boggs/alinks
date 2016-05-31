<?php

namespace Drupal\alinks\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

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
class Alink extends ConfigEntityBase implements AlinkInterface {

  /**
   * The Alink ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Alink label.
   *
   * @var string
   */
  protected $label;

}

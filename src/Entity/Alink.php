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
   * The ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The label.
   *
   * @var string
   */
  protected $label;

  /**
   * The start boundary.
   *
   * @var int
   */
  protected $start_boundary;

  /**
   * The link text.
   *
   * @var string
   */
  protected $text;

  /**
   * The end boundary.
   *
   * @var int
   */
  protected $end_boundary;

  /**
   * Case insensitive matching.
   *
   * @var int
   */
  protected $case_insensitive;

  /**
   * The link url.
   *
   * @var string
   */
  protected $url;

  /**
   * The link title.
   *
   * @var string
   */
  protected $url_title;

  /**
   * Is external link.
   *
   * @var int
   */
  protected $external;

  /**
   * The link class.
   *
   * @var string
   */
  protected $class;

  /**
   * The link weight.
   *
   * @var int
   */
  protected $weight;

  /**
   * {@inheritdoc}
   */
  public function getStartBoundary() {
    return $this->start_boundary;
  }

  /**
   * {@inheritdoc}
   */
  public function getText() {
    return $this->text;
  }

  /**
   * {@inheritdoc}
   */
  public function getEndBoundary() {
    return $this->end_boundary;
  }

  /**
   * {@inheritdoc}
   */
  public function getCaseInsensitive() {
    return $this->case_insensitive;
  }

  /**
   * {@inheritdoc}
   */
  public function getUrl() {
    return $this->url;
  }

  /**
   * {@inheritdoc}
   */
  public function getUrlTitle() {
    return $this->url_title;
  }

  /**
   * {@inheritdoc}
   */
  public function getExternal() {
    return $this->external;
  }

  /**
   * {@inheritdoc}
   */
  public function getClass() {
    return $this->class;
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->weight;
  }
}

<?php

/**
 * @file
 * Contains \Drupal\easy_breadcrumb\Form\EasyBreadcrumbGeneralSettingsForm.
 */

namespace Drupal\alinks\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\easy_breadcrumb\EasyBreadcrumbConstants;

/**
 *  Build Alinks settings form.
 */
class AlinksSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'alinks_settings';
  }

  /**
   * {@inheritdoc}.
   */
  protected function getEditableConfigNames() {
    return ['alinks.settings'];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('alinks.settings');
    $form = [];

    // Fieldset for grouping general settings fields.
    $fieldset = [
      '#type' => 'fieldset',
      '#title' => t('Settings'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    // Get node types and create array for options field
    $types = node_type_get_types();
    $types_options = array();
    foreach ($types as $k => $v) {
      $types_options[$k] = $v->name;
    }
    $fieldset['alinks_node_types'] = [
      '#type' => 'fieldset',
      '#title' => t('Alinks node types'),
      '#description' => t('Choose the node types into which Alinks will automatically insert links.'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $fieldset['alinks_node_types']['alinks_node_types'] = [
      '#type' => 'checkboxes',
      '#title' => t('Alinks node types'),
      '#description' => t('Select node types'),
      '#options' => $types_options,
      '#default_value' => $config->get('alinks_node_types'),
    ];
    $fieldset['alinks_limit'] = [
      '#type' => 'textfield',
      '#size' => 5,
      '#maxlenghth' => 3,
      '#title' => t('Alinks limit'),
      '#description' => t('Set the maxium instances an alink can replace. Use -1 if you want all instances in the node to be replaced.'),
      '#default_value' => $config->get('alinks_limit'),
    ];

    // Inserts the fieldset for the settings fields.
    $form['alinks'] = $fieldset;

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('easy_breadcrumb.settings');

    $config
      ->set(EasyBreadcrumbConstants::INCLUDE_INVALID_PATHS, $form_state->getValue(EasyBreadcrumbConstants::INCLUDE_INVALID_PATHS))
      ->set(EasyBreadcrumbConstants::EXCLUDED_PATHS, $form_state->getValue(EasyBreadcrumbConstants::EXCLUDED_PATHS))
      ->set(EasyBreadcrumbConstants::SEGMENTS_SEPARATOR, $form_state->getValue(EasyBreadcrumbConstants::SEGMENTS_SEPARATOR))
      ->set(EasyBreadcrumbConstants::INCLUDE_HOME_SEGMENT, $form_state->getValue(EasyBreadcrumbConstants::INCLUDE_HOME_SEGMENT))
      ->set(EasyBreadcrumbConstants::HOME_SEGMENT_TITLE, $form_state->getValue(EasyBreadcrumbConstants::HOME_SEGMENT_TITLE))
      ->set(EasyBreadcrumbConstants::INCLUDE_TITLE_SEGMENT, $form_state->getValue(EasyBreadcrumbConstants::INCLUDE_TITLE_SEGMENT))
      ->set(EasyBreadcrumbConstants::TITLE_SEGMENT_AS_LINK, $form_state->getValue(EasyBreadcrumbConstants::TITLE_SEGMENT_AS_LINK))
      ->set(EasyBreadcrumbConstants::TITLE_FROM_PAGE_WHEN_AVAILABLE, $form_state->getValue(EasyBreadcrumbConstants::TITLE_FROM_PAGE_WHEN_AVAILABLE))
      //->set(EasyBreadcrumbConstants::CAPITALIZATOR_MODE, $form_state->getValue(EasyBreadcrumbConstants::CAPITALIZATOR_MODE))
      //->set(EasyBreadcrumbConstants::CAPITALIZATOR_IGNORED_WORDS, $form_state->getValue(EasyBreadcrumbConstants::CAPITALIZATOR_IGNORED_WORDS))
      ->save();

    parent::submitForm($form, $form_state);
  }
}

<?php

/**
 * @file
 * Contains \Drupal\easy_breadcrumb\Form\EasyBreadcrumbGeneralSettingsForm.
 */

namespace Drupal\alinks\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\node\Entity\NodeType;

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
    $types_options = [];
    $form = [];

    // Fieldset for grouping general settings fields.
    $fieldset = [
      '#type' => 'fieldset',
      '#title' => t('Settings'),
      '#collapsible' => FALSE,
    ];

    // Get node types and create array for options field
    $all_content_types = NodeType::loadMultiple();

    /** @var NodeType $content_type */
    foreach ($all_content_types as $machine_name => $content_type) {
      $types_options[$machine_name] = $content_type->label();
    }

    $fieldset['node_types'] = [
      '#type' => 'fieldset',
      '#title' => t('Alinks node types'),
      '#description' => t('Choose the node types into which Alinks will automatically insert links.'),
      '#collapsible' => FALSE,
    ];
    $fieldset['node_types']['node_types'] = [
      '#type' => 'checkboxes',
      '#title' => t('Node types'),
      '#description' => t('Select content types to apply Alinks to'),
      '#options' => $types_options,
      '#default_value' => $config->get('node_types'),
    ];
    $fieldset['limit'] = [
      '#type' => 'textfield',
      '#size' => 5,
      '#maxlenghth' => 3,
      '#title' => t('Alinks limit'),
      '#description' => t('Set the maximum instances an Alink can replace. Use -1 if you want all instances in the node to be replaced.'),
      '#default_value' => $config->get('limit'),
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
    $config = $this->config('alinks.settings');

    $config
      ->set('node_types', $form_state->getValue('node_types'))
      ->set('limit', $form_state->getValue('limit'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}

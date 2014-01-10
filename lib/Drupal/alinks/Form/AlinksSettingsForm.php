<?php

/**
 * @file
 * Contains \Drupal\alinks\Form\AlinksSettingsForm.
 */

namespace Drupal\alinks\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Cache\Cache;

/**
 * Configure alinks settings for this site.
 */
class AlinksSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'alinks_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $config = $this->config('alinks.settings');
    $form['alinks_type'] = array(
      '#type' => 'textfield',
      '#title' => t('Alinks limit'),
      '#size' => 5,
      '#maxlenghth' => 3,
      '#default_value' => $config->get('alinks_type'),
      '#description' => t('Set the maxium instances an alink can replace. Use -1 if you want all instances in the node to be replaced.'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $config = $this->config('alinks.settings');
    $config->set('alinks_type', $form_state['values']['alinks_type']);
    $config->save();
    parent::submitForm($form, $form_state);

    // @todo Decouple from form: http://drupal.org/node/2040135.
    Cache::invalidateTags(array('config' => 'alinks.settings'));
  }
}

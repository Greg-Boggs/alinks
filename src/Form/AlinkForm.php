<?php

namespace Drupal\alinks\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AlinkForm.
 *
 * @package Drupal\alinks\Form
 */
class AlinkForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $alink = $this->entity;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $alink->label(),
      '#description' => $this->t("Label for the Alink."),
      '#required' => TRUE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $alink->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\alinks\Entity\Alink::load',
      ),
      '#disabled' => !$alink->isNew(),
    );

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $alink = $this->entity;
    $status = $alink->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Alink.', [
          '%label' => $alink->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Alink.', [
          '%label' => $alink->label(),
        ]));
    }
    $form_state->setRedirectUrl($alink->urlInfo('collection'));
  }

}

<?php

namespace Drupal\alinks\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Url;

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

    // Change page title for the edit operation
    if ($this->operation == 'edit') {
      $form['#title'] = $this->t('Edit alink: @label', array('@label' => $alink->label));
    }
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $alink->label(),
      '#description' => $this->t('Label for the Alink.'),
      '#required' => TRUE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#default_value' => $alink->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\alinks\Entity\Alink::load',
      ),
      '#disabled' => !$alink->isNew(),
    );

    $form['start_boundary'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Start Boundary'),
      '#default_value' => $alink->getStartBoundary(),
      '#description' => $this->t('Enable if the string doesn\'t start with an alphanumeric or underscore character.'),
      '#required' => FALSE,
    ];

    $form['text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text'),
      '#maxlength' => 255,
      '#default_value' => $alink->getText(),
      '#description' => $this->t('The text to automatically replace with this link.'),
      '#required' => TRUE,
    ];

    $form['end_boundary'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('End Boundary'),
      '#default_value' => $alink->getEndBoundary(),
      '#description' => $this->t('Enable if the string doesn\'t end with an alphanumeric or underscore character.'),
      '#required' => FALSE,
    ];
    
    $form['case_insensitive'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Case Insensitive'),
      '#default_value' => $alink->getCaseIsensitive(),
      '#description' => $this->t('By default alinks are CaSe SeNsItIvE.'),
      '#required' => FALSE,
    ];

    $form['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL'),
      '#maxlength' => 255,
      '#default_value' => $alink->getUrl(),
      '#description' => $this->t('The href of the link. Internal (Drupal) links take the form "my/page"'),
      '#required' => TRUE,
    ];

    $form['url_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL Title'),
      '#maxlength' => 255,
      '#default_value' => $alink->getUrlTitle(),
      '#description' => $this->t('Title attribute of the URL.'),
      '#required' => FALSE,
    ];

    $form['external'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('External Link'),
      '#default_value' => $alink->getExternal(),
      '#description' => $this->t('Check this if the link is to an external domain and be sure to include http:// in the link.'),
      '#required' => FALSE,
    ];

    $form['class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link Class'),
      '#maxlength' => 255,
      '#default_value' => $alink->getClass(),
      '#description' => $this->t('Add a class to the link.'),
      '#required' => FALSE,
    ];

    $form['weight'] = [
      '#type' => 'weight',
      '#title' => $this->t('Weight'),
      '#default_value' => $alink->getWeight(),
      '#description' => $this->t('The lowest weight alink wins when there are multiple matches.'),
      '#required' => TRUE,
    ];

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

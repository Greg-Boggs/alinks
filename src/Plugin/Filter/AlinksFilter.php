<?php

namespace Drupal\alinks\Plugin\Filter;

use Drupal\alinks;
use Drupal\Core\Form\FormStateInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Url;

/**
 * Provides a filter to apply automatic links to a field.
 *
 * @Filter(
 *   id = "AlinksFilter",
 *   title = @Translation("Alinks"),
 *   description = @Translation("Adds automatic links"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
 *   settings = {
 *     "limit" = 1,
 *   },
 *   weight = 10
 * )
 */
class AlinksFilter extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $settings =  $this->settings;


    $form['help'] = array(
      '#type' => 'markup',
      '#value' => '<p>' . t("Enable automatic links") . '</p>',
    );


    // Replace space_hyphens with em-dash.
    $form['limit'] = array(
      '#type' => 'textfield',
      '#title' => t('Limit links'),
      '#description' => t('Use -1 for unlimited links.'),
      '#default_value' => $settings['limit'],
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    parent::setConfiguration($configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    print('<h1>works</h1>');
    $settings =  $this->settings;
    $limit = $settings['limit'];

    $words = $this::get_alinks();
    if ($words) {
      if (is_array($words) && !empty($words) && isset($text)) {
        $text = alinks_make_links($text, $words);
      }
    }

    return new FilterProcessResult($text);
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    $settings = $this->settings;
    $output = '';

    if ($long) {
      $output = t('Automatic links will be added.');
    }

    return $output;
  }

  /**
   * Gets an array of all alinks.
   *
   * @return
   *   Array of alinks
   */
  private function get_alinks() {
    $alinks = Alink::loadMultiple();

    foreach ($alinks as $name => $alink) {
      $alinks[$name] = $alink;
    }

    return $alinks;
  }
}
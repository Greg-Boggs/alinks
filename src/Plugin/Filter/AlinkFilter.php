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
    $settings =  $this->settings;
    $limit = $settings['limit'];




    return new FilterProcessResult($text);
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    $settings = $this->settings;

    if ($long) {
      module_load_include('php', 'typogrify', 'unicode-conversion');

      $output = t('Typogrify.module brings the typographic refinements of Typogrify to Drupal.');
      $output .= '<ul>';
      if ($settings['wrap_ampersand']) {
        $output .= '<li>' . t('Wraps ampersands (the “&amp;” character) with !span.', array('!span' => '<code>&lt;span class="amp"&gt;&amp;&lt;/span&gt;</code>')) . '</li>';
      }
      if ($settings['widont_enabled']) {
        $output .= '<li>' . t("Prevents single words from wrapping onto their own line using Shaun Inman's Widont technique.") . '</li>';
      }
      if ($settings['wrap_initial_quotes']) {
        $output .= '<li>' . t("Converts straight quotation marks to typographer's quotation marks, using SmartyPants.");
        $output .= '</li><li>' . t('Wraps initial quotation marks with !quote or !dquote.', array(
              '!quote' => '<code>&lt;span class="quo"&gt;&lt;/span&gt;</code>',
              '!dquote' => '<code>&lt;span class="dquo"&gt;&lt;/span&gt;</code>')
          ) . '</li>';
      }
      $output .= t('<li>Converts multiple hyphens to en dashes and em dashes (according to your preferences), using SmartyPants.</li>');
      if ($settings['hyphenate_shy']) {
        $output .= '<li>' . t('Words may be broken at the hyphenation points marked by “=”.') . '</li>';
      }
      if ($settings['wrap_abbr']) {
        $output .= '<li>' . t('Wraps abbreviations as “e.g.” to !span and adds a thin space (1/6 em) after the dots.</li>', array('!span' => '<code>&lt;span class="abbr"&gt;e.g.&lt;/span&gt;</code>')) . '</li>';
      }
      if ($settings['wrap_numbers']) {
        $output .= '<li>' . t('Wraps large numbers &gt; 1&thinsp;000 with !span and inserts thin space for digit grouping.', array('!span' => '<code>&lt;span class="number"&gt;…&lt;/span&gt;</code>')) . '</li>';
      }
      if ($settings['wrap_caps']) {
        $output .= '<li>' . t('Wraps multiple capital letters with !span.', array('!span' => '<code>&lt;span class="caps"&gt;CAPS&lt;/span&gt;</code>')) . '</li>';
      }
      $output .= '<li>' . t('Adds a css style sheet that uses the &lt;span&gt; tags to substitute a showy ampersand in headlines, switch caps to small caps, and hang initial quotation marks.') . '</li>';
      // Build a list of quotation marks to convert.
      foreach (unicode_conversion_map('quotes') as $ascii => $unicode) {
        if ($settings['quotes'][$ascii]) {
          $output .= '<li>' . t('Converts <code>!ascii</code> to !unicode', array(
              '!ascii' => $ascii,
              '!unicode' => $unicode,
            )) . "</li>\n";
        }
      }
      $output .= '</ul>';
    }
    else {
      $output = t('Typographic refinements will be added.');
    }

    return $output;
  }
}
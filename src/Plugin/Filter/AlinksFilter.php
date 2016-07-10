<?php

namespace Drupal\alinks\Plugin\Filter;

use Drupal\alinks\Entity\Alink;
use Drupal\Core\Form\FormStateInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Link;
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
    print('<h1>run</h1>');
    $settings =  $this->settings;
    $limit = $settings['limit'];

    $words = $this->getAlinks();
    if ($words) {
      if (is_array($words) && !empty($words) && isset($text)) {
        $text = $this->processLinks($text, $words);
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
  private function getAlinks() {
    $alinks = Alink::loadMultiple();

    foreach ($alinks as $name => $alink) {
      $alinks[$name] = $alink;
    }

    return $alinks;
  }

  /**
   * Transform the first instance of any word defined to links
   */
  private function processLinks($body, $words) {
    if (is_array($words) && isset($body)) {

      // create the replacement array
      $path = $_GET['q'];
      $url = \Drupal::service('path.alias_manager')->getAliasByPath('/' . $path);
      $i = 0;
      $title = 'alink_replaced\1alink_replaced';
      $alink_options = array();
      $links_chars = array('/', '-');
      $links_chars_replace = array('alink_slash', 'alink_minus');
      $replacement = array();

      foreach ($words as $word) {
        if ($word->getUrl() != $url) {
          $alink_start_boundary = ($word->getStartBoundary() == 1) ? 'B' : 'b';
          $alink_end_boundary = ($word->getEndBoundary() == 1) ? 'B' : 'b';
          $alink_case_insensivity = ($word->getCaseInsensitive() == 1) ? 'i' : '';

          $alink_text[] = '$\\' . $alink_start_boundary . '(' . preg_quote($word->getText(), '$') . ')\\' . $alink_end_boundary . '(?!((?!alink_replaced).)*alink_replaced</a>)$u' . $alink_case_insensivity;

          if ($word->alink_external != 1) {
            $alink_path = 'alink_check' . str_replace('/', 'alink_slash', $word->getUrl()) . 'alink_check';
          }
          else {
            $alink_path = str_replace($links_chars, $links_chars_replace, $word->getUrl()) . 'alink_check';
            $alink_options['absolute'] =  TRUE;
          }
          if (!empty($word->getClass())) {
            $alink_class = 'alink_check' . str_replace(' ', 'alink_space', $word->getClass()) . 'alink_check';
            $alink_options['attributes']['class'] = $alink_class;
          }
          if (!empty($word->getUrlTitle())) {
            $alink_title = 'alink_check' . str_replace(' ', 'alink_space', SafeMarkup::checkPlain($word->getUrlTitle())) . 'alink_check';
            $alink_options['attributes']['title'] = $alink_title;
          }
          $url = Url::fromUri($alink_path);
          $alink_url[] = new Link($title, $url, $alink_options);

          $i++;
        }
      }
      if ($i > 0) {
        $alink_url = str_replace(array('&amp;amp;', '&amp;lt;', '&amp;gt;'), array('&amp;', '&lt;', '&gt;'), $alink_url);

        // we replace new lines with a temporary delimiter
        $carriage  = array("\r\n", "\n", "\r");
        $carriage_replacement = array(" cariage_replacement_rn ", " cariage_replacement_n ", " cariage_replacement_r ");
        $body = str_replace($carriage, $carriage_replacement, $body);

        // we get out the already existing links
        preg_match_all('/\<a\s.*?\>(.*?)\<\/a\>/i', $body, $linka);

        // create the replacement array
        foreach ($linka[0] as $key => $values) {
          $replacement[] = ' alink_delimiter_' . $key . ' ';
        }

        // replace the links with the replacement text
        $body = str_replace($linka[0], $replacement, $body);

        // we get all the text that is not inside a html tag from the modified text
        preg_match_all('/\>(.*?)\</', $body, $output);
        $output[0] = array_unique($output[0]);
        $output[1] = array_unique($output[1]);

        // transform the result array to a string so we can use the limit argument
        $text = implode(' alink_delimiter_one_string ', $output[1]);
        $limit = \Drupal::config('alinks.settings')->get('alinks_limit');

        // make the actual replacement
        if ($limit == -1) {
          $output[1] = preg_replace($alink_text, $alink_url, $text);
        }
        else {
          $output[1] = preg_replace($alink_text, $alink_url, $text, $limit);
        }

        // rebuild the array
        $output[1] = explode(' alink_delimiter_one_string ', $output[1]);
        $our_output = array();
        $i = 0;

        // we make sure the text will pe replaced outside any tag
        foreach ($output[1] as $key => $values) {
          if (!$values) {
            $our_output[$i] = '><';
          }
          else {
            $our_output[$i] = str_replace($values, '>' . $values . '<', $values);
          }
          $i++;
        }

        // insert the new text in the full text
        $body = str_replace($output[0], $our_output, $body);

        // and put back the links in the text
        $body = str_replace($replacement, $linka[0], $body);
        $body = str_replace('alink_check', '', $body);
        $body = str_replace('alink_replaced', '', $body);
        $body = str_replace('alink_space', ' ', $body);
        $body = str_replace($links_chars_replace, $links_chars, $body);

        // and finaly put back the new lines
        $body = str_replace($carriage_replacement, $carriage, $body);
      }
    }

    return $body;
  }

}
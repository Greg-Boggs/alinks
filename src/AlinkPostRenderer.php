<?php

namespace Drupal\alinks;

use Drupal\alinks\Entity\Keyword;
use Drupal\Component\Utility\Html;
use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;
use Wamania\Snowball\English;

/**
 * Class AlinkPostRenderer.
 */
class AlinkPostRenderer {

  protected $content;

  protected $keywords;

  protected $existingLinks;

  /**
   * Stemmer.
   *
   * @var \Wamania\Snowball\Stem $stemmer
   */
  protected $stemmer;

  protected $stemmerCache = [];

  protected $xpathSelector = "//text()[not(ancestor::a) and not(ancestor::script) and not(ancestor::*[@data-alink-ignore])]";

  /**
   * AlinkPostRenderer constructor.
   * @param $content
   * @param $context
   * @param null $xpathSelector
   */
  public function __construct($content, $context = NULL, $xpathSelector = NULL) {

    if (!empty($context['#entity_type']) && !empty($context['#' . $context['#entity_type']])) {
      /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
      $entity = $context['#' . $context['#entity_type']];
      $class = 'Wamania\Snowball\\' . $entity->language()->getName();
      if (class_exists($class)) {
        $this->stemmer = new $class();
      }
      else {
        $this->stemmer = new English();
      }
    }

    $this->content = $content;
    if ($xpathSelector) {
      $this->xpathSelector = $xpathSelector;
    }
  }

  /**
   * @return \Drupal\alinks\Entity\Keyword[]
   */
  protected function getKeywords() {
    if ($this->keywords === NULL) {
      $ids = \Drupal::entityQuery('alink_keyword')
        ->condition('status', 1)
        ->execute();
      $this->keywords = Keyword::loadMultiple($ids);


      $vocabularies = \Drupal::config('alinks.settings')->get('vocabularies');

      if ($vocabularies) {
        $terms = \Drupal::entityQuery('taxonomy_term')
          ->condition('vid', $vocabularies, 'IN')
          ->execute();

        /** @var Term[] $terms */
        $terms = Term::loadMultiple($terms);
        foreach ($terms as $term) {
          $this->keywords[] = Keyword::create([
            'name' => $term->label(),
            'link' => [
              'uri' => 'internal:/' . $term->toUrl()->getInternalPath(),
            ],
          ]);
        }
      }

      foreach ($this->keywords as &$keyword) {
        $keyword->stemmed_keyword = $this->stemmer->stem($keyword->getText());
      }
    }
    return $this->keywords;
  }

  /**
   * @param mixed $keywords
   */
  public function setKeywords($keywords) {
    $this->keywords = $keywords;
    foreach ($this->keywords as &$keyword) {
      $keyword->stemmed_keyword = $this->stemmer->stem($keyword->getText());
    }
  }

  public function replace() {
    $dom = Html::load($this->content);
    $xpath = new \DOMXPath($dom);

    $this->existingLinks = $this->extractExistingLinks($xpath);
    /** @var Keyword[] $words */
    $this->keywords = array_filter($this->getKeywords(), function (Keyword $word) {
      return !isset($this->existingLinks[$word->getUrl()]);
    });

    foreach ($xpath->query($this->xpathSelector) as $node) {
      $text = $node->wholeText;
      $replace = FALSE;
      if (empty(trim($text))) {
        continue;
      }
      foreach ($this->keywords as $key => $word) {
        // @TODO: Make it configurable replaceAll vs. replaceFirst
        $text = $this->replaceFirst($word, '<a href="' . $word->getUrl() . '">' . $word->getText() . '</a>', $text, $count);
        if ($count) {
          $replace = TRUE;
          $this->addExistingLink($word);
          break;
        }
      }
      if ($replace) {
        $this->replaceNodeContent($node, $text);
      }
    }

    return Html::serialize($dom);
  }

  protected function processDomNodeList($element) {
    foreach ($element as $item) {
      if ($item instanceof \DOMElement) {
        if ($item->hasChildNodes()) {
          foreach ($item->childNodes as $childNode) {
            if ($childNode instanceof \DOMText) {
              foreach ($this->getKeywords() as $word) {
                // @TODO: Make it configurable replaceAll vs. replaceFirst
                $childNode->nodeValue = $this->replaceFirst($word, '<a href="' . $word->getUrl() . '">' . $word->getText() . '</a>', $childNode->nodeValue);
              }
            }
          }
        }
      }
    }
    return $element;
  }

  protected function replaceAll(Keyword $search, $replace, $subject, &$count = 0) {
    $subject = str_replace($search->getText(), $replace, $subject, $count);
    if ($count == 0) {
      // @todo: Try stemmer
    }
    return $subject;
  }

  protected function replaceFirst(Keyword $search, $replace, $subject, &$count = 0) {
    $search_escaped = preg_quote($search->getText(), '/');
    $subject = preg_replace('/\b' . $search_escaped . '\b/u', $replace, $subject, 1, $count);
    if ($count == 0) {
      // @TODO: Look at Search API Tokenizer & Highlighter
      $terms = str_replace(['.', ',', ';', '!', '?'], '', $subject);
      $terms = explode(' ', $terms);
      $terms = array_filter(array_map('trim', $terms));
      $terms = array_combine($terms, $terms);
      $terms = array_map(function ($term) {
        if (!isset($this->stemmerCache[$term])) {
          $this->stemmerCache[$term] = $this->stemmer->stem($term);
        }
        return $this->stemmerCache[$term];
      }, $terms);
      foreach ($terms as $original_term => $term) {
        if ($term === $search->stemmed_keyword) {
          $search_escaped = preg_quote($original_term, '/');
          $subject = preg_replace('/\b' . $search_escaped . '\b/u', '<a href="' . $search->getUrl() . '">' . $original_term . '</a>', $subject, 1, $count);
        }
      }
    }
    return $subject;
  }

  public static function postRender($content, $context) {
    $selector = "//*//p//text()[not(ancestor::a) and not(ancestor::script) and not(ancestor::*[@data-alink-ignore])]";
    $renderer = new static($content, $context, $selector);
    return $renderer->replace();
  }

  /**
   * @param $uri
   * @return string
   */
  protected function normalizeUri($uri) {
    // If we already have a scheme, we're fine.
    if (empty($uri) || !is_null(parse_url($uri, PHP_URL_SCHEME))) {
      return $uri;
    }

    // Remove the <front> component of the URL.
    if (strpos($uri, '<front>') === 0) {
      $uri = substr($uri, strlen('<front>'));
    }

    // Add the internal: scheme and ensure a leading slash.
    return 'internal:/' . ltrim($uri, '/');
  }

  /**
   * @param $xpath
   * @return array
   */
  protected function extractExistingLinks($xpath) {
    // @TODO: Remove keywords with links which are already in the text
    $links = [];
    foreach ($xpath->query('//a') as $link) {
      try {
        $uri = $this->normalizeUri($link->getAttribute('href'));
        $links[] = Url::fromUri($uri)->toString();
      } catch (\Exception $exception) {
        // Do nothing.
      }
    }
    return array_flip(array_unique($links));
  }

  protected function addExistingLink(Keyword $word) {
    $this->existingLinks[$word->getUrl()] = TRUE;
    $this->keywords = array_filter($this->keywords, function ($keyword) use ($word) {
      if ($keyword->getText() == $word->getText()) {
        return FALSE;
      }
      if ($keyword->getUrl() == $word->getUrl()) {
        return FALSE;
      }
      return TRUE;
    });
  }

  /**
   * Replace the contents of a DOMNode.
   *
   * @param \DOMNode $node
   *   A DOMNode object.
   * @param string $content
   *   The text or HTML that will replace the contents of $node.
   */
  protected function replaceNodeContent(\DOMNode &$node, $content) {
    if (strlen($content)) {
      // Load the content into a new DOMDocument and retrieve the DOM nodes.
      $replacement_nodes = Html::load($content)->getElementsByTagName('body')
        ->item(0)
        ->childNodes;
    }
    else {
      $replacement_nodes = [$node->ownerDocument->createTextNode('')];
    }

    foreach ($replacement_nodes as $replacement_node) {
      // Import the replacement node from the new DOMDocument into the original
      // one, importing also the child nodes of the replacement node.
      $replacement_node = $node->ownerDocument->importNode($replacement_node, TRUE);
      $node->parentNode->insertBefore($replacement_node, $node);
    }
    $node->parentNode->removeChild($node);
  }

}

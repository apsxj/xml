<?php

/**
 * XML Segment Object
 *
 * @category  Server Software
 * @package   apsxj/xml
 * @author    Roderic Linguri <apsxj@mail.com>
 * @copyright 2021 Roderic Linguri
 * @license   https://github.com/apsxj/xml/blob/main/LICENSE MIT
 * @link      https://github.com/apsxj/xml
 * @version   0.1.0
 * @since     0.1.0
 */

namespace apsxj\xml;

class Segment
{
  /** @property string html tag */
  public $tag;

  /** @property boolean whether or not this is the closing tag */
  public $closing;

  /** @property boolean whether or not this segment represents a void/singleton/empty element */
  public $empty;

  /** @property mixed tag attributes */
  public $attrs;

  /** @property string content */
  public $innerHTML;

  /**
   * Constructor
   * 
   * @param string piece of html split by '<'
   */
  public function __construct($string)
  {

    $string = trim(preg_replace("/\s+/", ' ', $string));

    // Split at the closing tag to isolate innerHTML
    $pieces = explode('>', $string);

    // First chunk is tag and attributes
    $string = trim(array_shift($pieces));

    // Recombine everything else into the innerHTML
    $this->innerHTML = trim(implode('>', $pieces));

    // Check if it's a closing tag and strip out slashes
    if (substr($string, 0, 1) == '/') {
      $this->closing = true;
      $string = substr($string, 1);
    } else {
      $this->closing = false;
    }

    // Check if this might be a declared as empty with a trailing slash
    if (substr($string, -1) == '/') {
      $this->empty = true;
      $string = rtrim($string, '/');
    }

    if ($this->closing) {
      // Closing tag, segment will just be the tag for matching
      $this->tag = trim($string);
    } else {
      // re-split by space
      $xmlpieces = explode(' ', $string);

      // Tag will always be the first piece
      $this->tag = array_shift($xmlpieces);

      // Cancatenate whatever remains and pass to the attributes helper
      $this->attrs = Segment::attrs(implode(' ', $xmlpieces));

      // Check if this is html5 empty or mdo, so we don't try to match a closing tag
      if (in_array($this->tag, XML::voidTags()) || substr($this->tag, 0, 1) == '!') {
        $this->empty = true;
      } else {
        $this->empty = false;
      }
    }
  }

  /**
   * Convert attributes string into an associative array
   * 
   * @param string attributes
   * 
   * @return mixed attributes
   */
  public static function attrs($string)
  {
    // Set up an empty object to set key-value pairs on
    $attrs = array();

    // Handle case where single quotes are used for attributes
    $quote = strpos($string, '"') ? '"' : "'";

    // Split at the delimiter that isolates key-value pairs
    $pieces = explode($quote . ' ', $string);

    // Calculate the key-value pair to set on the object
    foreach ($pieces as $piece) {
      // Split by the delimiter that separates the key from the value
      $values = explode('=', $piece);

      // Key is always the first piece
      $key = trim(array_shift($values));

      // Value is whatever is left, restoring any '=' that were in the value
      $value = implode('=', $values);

      // Make sure there is a key
      if (strlen($key) > 0) {
        // Handle singleton attributes
        if (strlen($value) == 0) {
          $value = $key;
        }

        // filter out quotes on the value
        $attrs[$key] = str_replace($quote, '', $value);
      }
    }

    return $attrs;
  }
}

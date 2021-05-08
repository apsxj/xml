<?php

/**
 * XML Node Object
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

class Node
{
  /** @property integer node type XML::TEXT | XML::ELEMENT */
  protected $type;

  /** @property string the html tag */
  protected $tag;

  /** @property mixed an associative array of attributes */
  protected $attributes;

  /** @property array of child Node objects */
  protected $children;

  /** @property string the text of a text node */
  protected $text;

  /**
   * Constructor
   *
   * @param string $arg
   * @param integer $type
   * @param array $attributes
   * @param array $children
   */
  public function __construct($arg, $type = XML::TEXT, $attributes = array(), $children = array())
  {
    $this->type = $type;

    if ($type == XML::TEXT) {
      $this->text = $arg;
    } else {
      $this->tag = $arg;
      $this->attributes = $attributes;
      $this->children = $children;
    }
  }

  /* Getters */

  public function getType()
  {
    return $this->type;
  }

  public function getTag()
  {
    return $this->tag;
  }

  public function getAttributes()
  {
    return $this->attributes;
  }

  public function getChildren()
  {
    return $this->children;
  }

  public function setChildren($children)
  {
    $this->children = $children;
  }

  public function appendChild($child)
  {
    array_push($this->children, $child);
  }

  /**
   * Renders HTML hierarchy
   *
   * @return string HTML
   */
  public function xml()
  {

    if ($this->type == XML::TEXT) {
      return $this->text;
    } else {

      $str = PHP_EOL . '<' . $this->tag;

      foreach ($this->attributes as $k => $v) {
        if ($k == $v) {
          $str .= ' ' . $k;
        } else {
          $str .= ' ' . $k . '="' . $v . '"';
        }
      }

      $str .= '>';

      if (!in_array($this->tag, XML::voidTags()) && substr($this->tag, 0, 1) != '!') {

        $cr = '';

        foreach ($this->children as $child) {

          if ($child->getType() == XML::ELEMENT) {
            $cr = PHP_EOL;
          }
          $str .= $child->xml();
        }

        $str .= $cr . '</' . $this->tag . '>';
      }

      return $str;
    }
  }

  /**
   * Factory method to create an element node
   *
   * @param string $tag
   * @param array $attributes
   * @param array $children
   * 
   * @return object Node
   */
  public static function element(
    $tag,
    $attributes = array(),
    $children = array()
  ) {
    return new Node($tag, XML::ELEMENT, $attributes, $children);
  }

  /**
   * Factory method to create a text node
   *
   * @param string $text
   * 
   * @return object Node
   */
  public static function text($text)
  {
    return new Node($text);
  }

  /**
   * Return this node as an associative array
   * 
   * @return mixed
   */
  public function assoc()
  {
    $children = array();
    if (isset($this->children)) {
      foreach ($this->children as $child) {
        array_push($children, $child->assoc());
      }
    }

    $attributes = new stdClass();
    if (isset($this->attributes)) {
      if (count($this->attributes) > 0) {
        $attributes = $this->attributes;
      }
    }

    return array(
      'type' => $this->type,
      'tag' => $this->tag,
      'attributes' => $attributes,
      'children' => $children,
      'text' => $this->text
    );
  }
}

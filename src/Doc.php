<?php

/**
 * XML Doc Object
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

class Doc
{
  /** @property string xml declaration */
  public $declaration;

  /** @property object Node */
  public $doctype;

  /** @property object Node */
  public $root;

  /**
   * Constructor
   * 
   * @param object Node
   * @param object Node
   */
  public function __construct($doctype, $root, $declaration = false)
  {
    $this->declaration = $declaration;
    $this->doctype = $doctype;
    $this->root = $root;
  }

  /**
   * Factory method for creating a doc object with an HTML string
   * 
   * @param string valid XML
   * 
   * @return object XMLDoc instance
   */
  public static function docFromHTML($string)
  {
    $string = trim(preg_replace("/\s+/", ' ', $string));

    $segments = array();

    $pieces = explode('<', $string);

    foreach ($pieces as $piece) {
      if (strlen($piece) > 0) {
        array_push(
          $segments,
          new Segment($piece)
        );
      }
    }

    $nodes = self::nodesFromSegments($segments);

    // In some cases there may be a declaration
    $declaration = false;

    // Create the most basic doctype declaration in case the first node does not do so
    $doctype = Node::element('!DOCTYPE', array('text' => 'text'));

    // Create a default root element
    $root = Node::text('No content');

    // See if we have an xml declaration
    if (count($nodes) > 0) {
      if (strtolower($nodes[0]->getTag()) == '?xml') {
        $declaration = array_shift($nodes);
        $doctype = Node::element('!DOCTYPE', array('xml' => 'xml'));
      }
    }

    // See if we have a doctype declaration
    if (count($nodes) > 0) {
      if (strtolower($nodes[0]->getTag()) == '!doctype') {
        $doctype = array_shift($nodes);
      }
    }

    // If we have at least one element left, set it as the root node
    if (count($nodes) > 0) {
      $root = array_shift($nodes);
    }

    return new self($doctype, $root, $declaration);
  }

  /**
   * Recursive method to organize Segments into node hierarchies
   * 
   * @param array of Segment objects
   * 
   * @return array of Node objects
   */
  public static function nodesFromSegments($segments)
  {
    // Set up the array of nodes to return
    $nodes = array();

    // Declare the open node
    $openNode = false;

    // Set up an array to save segments that are not the open node
    $childSegments = array();

    // Iterate over the array of segments
    foreach ($segments as $segment) {
      if (!$openNode) {
        // No open node

        // Handle case where a segment could not parse any attributes
        if (!$segment->attrs) {
          $segment->attrs = array();
        }

        // Create a node
        $node = Node::element(
          $segment->tag,
          $segment->attrs
        );

        // Set any text stashed in innerHTML as a child text segment
        if (strlen($segment->innerHTML) > 0) {
          $node->setChildren(
            array(
              Node::text($segment->innerHTML)
            )
          );
        }

        // If this is empty/void/singleton
        if ($segment->empty) {
          // No need to open a node, just append to nodes
          array_push($nodes, $node);
        } else {
          // Open the node so we can stash child segments until we hit the closing tag
          $openNode = $node;
        }
      } else {
        // Have an open node
        if ($segment->closing && $segment->tag == $openNode->getTag()) {
          // This is the closing segment for the open node

          // Recusively set any child segments as nodes
          if (count($childSegments) > 0) {
            $openNode->setChildren(self::nodesFromSegments($childSegments));
          }

          // Append to nodes
          array_push($nodes, $openNode);

          // Reset open node and child segments for the next segment
          $openNode = false;
          $childSegments = array();
        } else {
          // Not the closing segment for the open node, stash it into child segments
          array_push($childSegments, $segment);
        }
      }
    }

    // If there is no closing tag for an element, prevent children from getting dropped
    if ($openNode) {
      if (count($childSegments) > 0) {
        $openNode->setChildren(self::nodesFromSegments($childSegments));
      }

      array_push($nodes, $openNode);
    }

    return $nodes;
  }

  /**
   * Returns this object as an associative array
   * 
   * @return mixed
   */
  public function assoc()
  {
    return array(
      'doctype' => $this->doctype->assoc(),
      'root' => $this->root->assoc()
    );
  }

  /**
   * Return hierarchy as renderable XML
   *
   * @return string xml / html
   */
  public function xml()
  {
    return $this->doctype->xml() . PHP_EOL . $this->root->xml();
  }
}

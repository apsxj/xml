<?php

/**
 * XML Response Object
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

class Response
{
  /** @property Doc the document to render */
  protected $doc;

  /**
   * Constructor
   *
   * @param Node $node
   */
  public function __construct($doc)
  {
    $this->doc = $doc;
  }

  /**
   * Render a node
   *
   * @param integer $status
   * @return void
   */
  public function render($status = 200)
  {
    header('Content-Type: text/html', true, $status);
    echo $this->doc->xml();
  }
}

<?php

/**
 * XML Base Class
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

class XML
{
  const TEXT = 1;

  const ELEMENT = 2;

  /**
   * Precomposed list of tags that represent void/empty/singleton elements
   * 
   * @return array of string html tags
   */
  public static function voidTags()
  {
    return array(
      'area',
      'base',
      'br',
      'col',
      'embed',
      'hr',
      'img',
      'input',
      'link',
      'meta',
      'param',
      'source',
      'track',
      'wbr',
      'command',
      'keygen',
      'menuitem'
    );
  }
}

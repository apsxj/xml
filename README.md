# xml #

_XML Parsing and Response Library_

### Getting Started ###

1. Add the following to your `composer.json` file:

```JavaScript
  "require": {
    "apsxj/xml": "dev-main"
  },
  "repositories": [
    {
      "type": "git",
      "url": "https://github.com/apsxj/xml.git"
    }
  ]
```

2. Run `composer install`

3. Before calling any of the methods, require the vendor autoloader

```PHP
// For example, from the root directory...
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
```

4. To create an `Response` object and render it:

```PHP
<?php

// unwrap classes from the apsxj\xml namespace
use apsxj\xml\Node;
use apsxj\xml\Doc;
use apsxj\xml\Response;

// Create the doctype declaration node
$doctype = Node::element(
  '!DOCTYPE',
  array(
    'html' => 'html'
  )
);

// Create the HTML node
$html = Node::element(
  'html',
  array(
    'lang' => 'en',
    'class' => 'h-100'
  ),
  array(
    Node::text('<head><title>It works!</title></head><body><h1>It works!</h1></body>')
  )
);

// Create the Document
$doc = new Doc($doctype, $html);

// Pass the document to a new Response object
$response = new Response($doc);

// Render the response
$response->render(200);
```
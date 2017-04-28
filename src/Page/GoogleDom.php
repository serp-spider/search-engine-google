<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Page;

use Psr\Http\Message\ResponseInterface;
use Serps\Core\Dom\WebPage;
use Serps\Core\Http\Proxy;
use Serps\Core\Http\ProxyInterface;
use Serps\Core\UrlArchive;
use Serps\SearchEngine\Google\GoogleUrl;
use Serps\SearchEngine\Google\GoogleUrlArchive;
use Serps\SearchEngine\Google\GoogleUrlInterface;

class GoogleDom extends WebPage
{

    protected $xpath;

    /**
     * @var \DOMDocument
     */
    protected $dom;

    /**
     * @var GoogleUrlInterface
     */
    protected $url;

    /**
     * store parsed json from parseJsonNode
     * @var array
     */
    private $parsedJsonStore = [];

    public function __construct($domString, GoogleUrlInterface $url)
    {
        $currentEncoding = $url->getParamValue('oe');
        if (!$currentEncoding) {
            $currentEncoding = 'UTF-8';
        }

        parent::__construct($domString, $url, $currentEncoding);
    }


    /**
     * Get a property from a google json node.
     * Google json nodes are invisible dom nodes that contain json text (found in mobile carousels for instance)
     *
     * @param string $propertyName name of the property to get
     * @param \DOMNode $node
     * @return mixed
     */
    public function getJsonNodeProperty($propertyName, \DOMNode $node)
    {
        $hash = spl_object_hash($node);

        if (!isset($this->parsedJsonStore[$hash])) {
            $nodeValue = $node->nodeValue;
            $nodeValue = trim($nodeValue, '"');

            $this->parsedJsonStore[$hash] = json_decode($nodeValue, true);
        }

        $item = $this->parsedJsonStore[$hash];

        if ($item && is_array($item) && isset($item[$propertyName])) {
            return $item[$propertyName];
        } else {
            return null;
        }
    }
}

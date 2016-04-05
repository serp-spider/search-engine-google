<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Page;

use Psr\Http\Message\ResponseInterface;
use Serps\Core\Http\Proxy;
use Serps\Core\Http\ProxyInterface;
use Serps\Core\UrlArchive;
use Serps\SearchEngine\Google\Css;
use Serps\SearchEngine\Google\GoogleUrl;
use Serps\SearchEngine\Google\GoogleUrlArchive;
use Serps\SearchEngine\Google\GoogleUrlInterface;

class GoogleDom
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

    public function __construct($domString, GoogleUrlInterface $url)
    {
        // Load DOM
        $this->dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($domString);
        libxml_use_internal_errors(false);
        libxml_clear_errors();

        $this->url = $url;
    }


    /**
     * get the object xpath to query it
     * @return \DOMXPath
     */
    public function getXpath()
    {
        if (null === $this->xpath) {
            $this->xpath=new \DOMXPath($this->dom);
        }
        return $this->xpath;
    }

    /**
     * @return \DOMDocument
     */
    public function getDom()
    {
        return $this->dom;
    }

    /**
     * Runs a xpath query against the wrapped dom object
     *
     * That's a shortcut for  \DOMXPath::query()
     *
     * @link http://php.net/manual/en/domxpath.query.php
     *
     * @param string $query the xpath query
     * @param \DOMNode|null $node the context node for the query, leave it null to query the root
     * @return \DOMNodeList
     */
    public function xpathQuery($query, $node = null)
    {
        return $this->getXpath()->query($query, $node);
    }

    /**
     * Runs a css query against the wrapped dom object. Internally the css will translate to xpath
     *
     * @link http://php.net/manual/en/domxpath.query.php
     *
     * @param string $query the css query
     * @param \DOMNode|null $node the context node for the query, leave it null to query the root
     * @return \DOMNodeList
     */
    public function cssQuery($query, $node = null)
    {
        return $this->getXpath()->query(Css::toXPath($query), $node);
    }

    /**
     * @return GoogleUrlInterface
     */
    public function getUrl()
    {
        return $this->url;
    }
}

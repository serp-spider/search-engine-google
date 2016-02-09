<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Page;

use Psr\Http\Message\ResponseInterface;
use Serps\Core\Http\Proxy;
use Serps\Core\Http\ProxyInterface;
use Serps\Core\UrlArchive;
use Serps\SearchEngine\Google\GoogleUrl;
use Serps\SearchEngine\Google\GoogleUrlArchive;

class GoogleDom
{

    protected $xpath;

    /**
     * @var \DOMDocument
     */
    protected $dom;

    /**
     * @var GoogleUrlArchive
     */
    protected $url;

    protected $effectiveUrl;
    protected $proxy;

    public function __construct($domString, GoogleUrlArchive $url, UrlArchive $effectiveUrl, Proxy $proxy = null)
    {
        $this->url = $url;

        // Load DOM
        $this->dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($domString);
        libxml_use_internal_errors(false);
        libxml_clear_errors();

        $this->proxy = $proxy;
        $this->effectiveUrl = $effectiveUrl;
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
     * @return GoogleUrlArchive
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return UrlArchive
     */
    public function getEffectiveUrl()
    {
        return $this->effectiveUrl;
    }

    /**
     * @return ProxyInterface
     */
    public function getProxy()
    {
        return $this->proxy;
    }
}

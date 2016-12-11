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

    public function __construct($domString, GoogleUrlInterface $url)
    {
        $currentEncoding = $url->getParamValue('oe');
        if (!$currentEncoding) {
            $currentEncoding = 'UTF-8';
        }

        parent::__construct($domString, $url, $currentEncoding);
    }
}

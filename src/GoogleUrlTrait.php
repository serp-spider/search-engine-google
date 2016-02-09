<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google;

use Psr\Http\Message\RequestInterface;
use Serps\Core\Url\QueryParam;
use Serps\SearchEngine\Google\GoogleUrl;
use Zend\Diactoros\Request;

/**
 * Contains the base methods describing a google url.
 * @see Serps\SearchEngine\Google\GoogleUrl
 * @see Serps\SearchEngine\Google\GoogleUrlArchive
 */
trait GoogleUrlTrait
{

    public abstract function getParamValue($param, $defaultValue = null);
    public abstract function buildUrl();
    public abstract function getParamRawValue($param, $defaultValue = null);
    public abstract function getHost();
    public abstract function getPath();
    public abstract function getScheme();
    public abstract function getParams();
    public abstract function getHash();

    /**
     * Get the number of the page.
     * @return int
     */
    public function getPage()
    {
        $resultsPerPage = $this->getResultsPerPage();
        return $this->getParamValue('start', 0) / ($resultsPerPage > 0 ? $resultsPerPage : 10);
    }

    /**
     * @return string
     */
    public function getLanguageRestriction()
    {
        return $this->getParamValue('lr', null);
    }

    /**
     * Get the number of results per pages
     * @return int the number of results per pages
     */
    public function getResultsPerPage()
    {
        return $this->getParamValue('num', 10);
    }

    /**
     * Get the google result type. That's the result type in the top bar 'all', 'images', 'videos'...
     * You can use the special constant beginning with ``RESULT_TYPE_`` e.g: ``GoogleUrl::RESULT_TYPE_IMAGES``
     * @return string
     */
    public function getResultType()
    {
        return $this->getParamValue('tbm', GoogleUrl::RESULT_TYPE_ALL);
    }

    /**
     * Get the keywords to search
     * @return string
     */
    public function getSearchTerm()
    {
        return $this->getParamRawValue('q');
    }

    /**
     *
     * @return RequestInterface
     */
    public function buildRequest()
    {
        $headers = [];
        if ($lr = $this->getLanguageRestriction()) {
            $headers['Accept-Language'] = $lr;
        }

        $request = new Request(
            $this->buildUrl(),
            'GET',
            'php://memory',
            $headers
        );

        return $request;
    }

    public function getArchive()
    {
        return new GoogleUrlArchive(
            $this->getHost(),
            $this->getPath(),
            $this->getScheme(),
            $this->getParams(),
            $this->getHash()
        );
    }
}

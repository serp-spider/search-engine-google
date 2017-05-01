<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google;

use Serps\Core\Url\QueryParam;
use Serps\SearchEngine\Google\GoogleUrl;

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
     * Get the number of the page, the pages are 1 indexed
     * @return int
     */
    public function getPage()
    {
        $resultsPerPage = $this->getResultsPerPage();
        return 1 + $this->getParamValue('start', 0) / ($resultsPerPage > 0 ? $resultsPerPage : 10);
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
     * Check whether or not the auto correction of search term is enabled
     * @return bool true if it enabled (it is by default)
     */
    public function getAutoCorrectionEnabled()
    {
        return 1 == $this->getParamValue('nfpr');
    }

    /**
     * Get the keywords to search
     * @return string
     */
    public function getSearchTerm()
    {
        return $this->getParamRawValue('q');
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

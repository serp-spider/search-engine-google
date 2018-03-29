<?php

namespace Serps\SearchEngine\Google;

use Serps\SearchEngine\Google\GoogleUrlTrait;
use Serps\Core\Url;

/**
 * A fluent builder for a google url
 */
class GoogleUrl extends Url implements GoogleUrlInterface
{
    use GoogleUrlTrait;

    const RESULT_TYPE_ALL  = 'all';
    const RESULT_TYPE_NEWS = 'nws';
    const RESULT_TYPE_VIDEOS = 'vid';
    const RESULT_TYPE_IMAGES = 'isch';
    const RESULT_TYPE_SHOPPING = 'shop';
    const RESULT_TYPE_BOOKS = 'bks';
    const RESULT_TYPE_APPS = 'app';
    const RESULT_TYPE_MAP = 'app';

    public function __construct(
        $host = 'www.google.com',
        $path = '/search',
        $scheme = 'https',
        array $query = [],
        $hash = '',
        $port = null,
        $user = null,
        $pass = null
    ) {
        parent::__construct($scheme, $host, $path, $query, $hash, $port, $user, $pass);
    }

    public static function build(
        $scheme = null,
        $host = null,
        $path = null,
        array $query = [],
        $hash = null,
        $port = null,
        $user = null,
        $pass = null
    ) {
        return new static(
            $host,
            $path,
            $scheme,
            $query,
            $hash,
            $port,
            $user,
            $pass
        );
    }

    /**
     * Set the 'lr' param for the search and auto prepend 'lang_' if not present
     * @param string $lang the lang to restrict with the format "lang_en" or "en"
     * @return $this
     */
    public function setLanguageRestriction($lang)
    {

        // lr format is lang_[ISO]
        // if $lang starts with "lang_" do nothing
        // else we prepend lang_
        if (substr($lang, 0, 5) !== 'lang_') {
            $lang = 'lang_' . $lang;
        }

        $this->setParam('lr', $lang);
        return $this;
    }

    /**
     * Set the page number of the page. Starting from 1
     * @param int $pageNumber
     * @return $this
     */
    public function setPage($pageNumber)
    {
        $pageNumber--;
        if ($pageNumber <= 0) {
            $this->removeParam('start');
        } else {
            $this->setParam('start', $pageNumber * $this->getResultsPerPage());
        }
        return $this;
    }

    /**
     * Changes the number of results per page. Between 1 and 100
     * @param int $number number of results per page
     */
    public function setResultsPerPage($number)
    {
        if ($number < 1) {
            $number = 1;
        } elseif ($number > 100) {
            // Google limits it too 100
            $number = 100;
        }

        // page backup (see below)
        $currentPage = $this->getPage();
        if ($number == 10) {
            $this->removeParam('num');
        } else {
            $this->setParam('num', $number);
        }
        // need to refresh the page because it's based on the index of the first item
        $this->setPage($currentPage);
    }

    /**
     * Set the keywords to search
     * @param $search
     * @return $this
     */
    public function setSearchTerm($search)
    {
        $this->setParam('q', $search);
        return $this;
    }

    /**
     * This allows to enable or disable google auto-correction
     * @param bool $enabled by default auto correction is enable, set it to false to disable it
     */
    public function setAutoCorrectionEnabled($enabled)
    {
        if ($enabled) {
            $this->removeParam('nfpr');
        } else {
            $this->setParam('nfpr', 1);
        }
    }

    /**
     * Sets the google result type. That's the result type in the top bar 'all', 'images', 'videos'...
     * You can use the special constant beginning with ``RESULT_TYPE_`` e.g: ``GoogleUrl::RESULT_TYPE_IMAGES``
     *
     * @param $resultType
     */
    public function setResultType($resultType)
    {
        if ($resultType == self::RESULT_TYPE_ALL) {
            $this->removeParam('tbm');
        } else {
            $this->setParam('tbm', $resultType);
        }
    }
}

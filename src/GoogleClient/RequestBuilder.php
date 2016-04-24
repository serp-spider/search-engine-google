<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\GoogleClient;

use Serps\SearchEngine\Google\GoogleUrlInterface;
use Zend\Diactoros\Request;

class RequestBuilder
{

    protected $acceptLanguageFromUrl = true;
    protected $defaultAcceptLanguage = 'en';
    protected $userAgent;

    public function buildRequest(GoogleUrlInterface $googleUrl)
    {
        $headers = [];
        if ($this->acceptLanguageFromUrl && $lr = $googleUrl->getLanguageRestriction()) {
            if (substr($lr, 0, 5) == 'lang_') {
                $lang = substr($lr, 5);
            } else {
                $lang = $lr;
            }
            $headers['Accept-Language'] = $lang;
        } elseif ($this->defaultAcceptLanguage) {
            $headers['Accept-Language'] = $this->defaultAcceptLanguage;
        }

        if (($userAgent = $this->getUserAgent())) {
            $headers['User-Agent'] = $userAgent;
        }

        $request = new Request(
            $googleUrl->buildUrl(),
            'GET',
            'php://memory',
            $headers
        );

        return $request;
    }

    /**
     * @return mixed
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param mixed $userAgent
     */
    public function setUserAgent($userAgent)
    {
        if (!is_string($userAgent) && !is_null($userAgent)) {
            throw new \InvalidArgumentException('User agent must be a string.');
        }
        $this->userAgent = $userAgent;
    }



    /**
     * @return boolean
     */
    public function isAcceptLanguageFromUrl()
    {
        return $this->acceptLanguageFromUrl;
    }

    /**
     * @param boolean $acceptLanguageFromUrl
     */
    public function setAcceptLanguageFromUrl($acceptLanguageFromUrl)
    {
        $this->acceptLanguageFromUrl = $acceptLanguageFromUrl;
    }

    /**
     * @return string
     */
    public function getDefaultAcceptLanguage()
    {
        return $this->defaultAcceptLanguage;
    }

    /**
     * @param string $defaultAcceptLanguage
     */
    public function setDefaultAcceptLanguage($defaultAcceptLanguage)
    {
        $this->defaultAcceptLanguage = $defaultAcceptLanguage;
    }
}

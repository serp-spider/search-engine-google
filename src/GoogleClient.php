<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google;

use Serps\Core\Captcha\CaptchaSolverInterface;
use Serps\Core\Cookie\ArrayCookieJar;
use Serps\Core\Cookie\CookieJarInterface;
use Serps\Core\Http\HttpClientInterface;
use Serps\Core\Http\Proxy;
use Serps\Core\UrlArchive;
use Serps\Exception;
use Serps\SearchEngine\Google\Exception\GoogleCaptchaException;
use Serps\SearchEngine\Google\Page\GoogleCaptcha;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Page\GoogleError;
use Serps\SearchEngine\Google\Page\GoogleSerp;
use Serps\SearchEngine\Google\GoogleUrl;
use Serps\SearchEngine\Google\GoogleUrlTrait;
use Zend\Diactoros\Request;

class GoogleClient
{

    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var CookieJarInterface
     */
    protected $cookieJar;

    protected $cookiesEnabled;

    /**
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->cookiesEnabled = false;
    }

    /**
     * Enable usage of cookies
     */
    public function enableCookies()
    {
        $this->cookiesEnabled = true;
    }

    /**
     * Disable usage of cookies
     */
    public function disableCookies()
    {
        $this->cookiesEnabled = false;
    }

    /**
     * @return CookieJarInterface
     */
    public function getCookieJar()
    {
        if (null == $this->cookieJar) {
            $this->cookieJar = new ArrayCookieJar();
        }
        return $this->cookieJar;
    }

    /**
     * @param CookieJarInterface $cookieJar
     */
    public function setCookieJar(CookieJarInterface $cookieJar)
    {
        $this->cookieJar = $cookieJar;
    }



    /**
     * @param GoogleUrlInterface $googleUrl
     * @param Proxy|null $proxy
     * @return GoogleDom|GoogleSerp
     * @throws Exception\CaptchaException
     * @throws Exception\PageNotFoundException
     * @throws Exception\RequestErrorException
     */
    public function query(GoogleUrlInterface $googleUrl, Proxy $proxy = null)
    {

        $cookieJar = $this->cookiesEnabled ? $this->getCookieJar() : null;

        $request = $googleUrl->buildRequest();
        $response = $this->client->sendRequest($request, $proxy, $cookieJar);
        
        $statusCode = $response->getHttpResponseStatus();
        $urlArchive = $googleUrl->getArchive();

        $effectiveUrl = GoogleUrlArchive::fromString($response->getEffectiveUrl()->__toString());

        if (200 == $statusCode) {

            switch ($urlArchive->getResultType()) {
                case GoogleUrl::RESULT_TYPE_ALL:
                    $dom = new GoogleSerp($response->getPageContent(), $urlArchive, $effectiveUrl, $proxy);
                    break;
                default:
                    $dom = new GoogleDom($response->getPageContent(), $urlArchive, $effectiveUrl, $proxy);
            }

            return $dom;
        } else {
            if (404 == $statusCode) {
                throw new Exception\PageNotFoundException();
            } else {
                $errorDom = new GoogleError($response->getPageContent(), $urlArchive, $effectiveUrl, $proxy);

                if ($errorDom->isCaptcha()) {
                    throw new GoogleCaptchaException(new GoogleCaptcha($errorDom));
                } else {
                    throw new Exception\RequestErrorException($errorDom);
                }
            }
        }

    }

    public function solveCaptcha($code, $id, Proxy $proxy)
    {
        // TODO
        throw new Exception('Not implemented');
    }
}

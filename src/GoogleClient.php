<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google;

use Serps\Core\Browser\BrowserInterface;
use Serps\Core\Cookie\ArrayCookieJar;
use Serps\Core\Cookie\CookieJarInterface;
use Serps\Core\Http\HttpClientInterface;
use Serps\Core\Http\Proxy;
use Serps\Core\UrlArchive;
use Serps\Exception;
use Serps\SearchEngine\Google\Exception\GoogleCaptchaException;
use Serps\SearchEngine\Google\Page\GoogleCaptcha;
use Serps\SearchEngine\Google\Page\GoogleError;
use Serps\SearchEngine\Google\Page\GoogleSerp;
use Serps\SearchEngine\Google\GoogleUrl;
use Serps\Exception\RequestError\PageNotFoundException;
use Serps\Exception\RequestError\RequestErrorException;
use Serps\Exception\RequestError\InvalidResponseException;

/**
 * Google client the handles google url routing, dom object constructions and request errors
 *
 */
class GoogleClient
{

    protected $defaultBrowser;

    public function __construct(BrowserInterface $browser = null)
    {
        $this->defaultBrowser = $browser;
    }

    /**
     * @param GoogleUrlInterface $googleUrl
     * @param BrowserInterface|null $browser
     * @return GoogleSerp
     * @throws Exception
     * @throws PageNotFoundException
     * @throws InvalidResponseException
     * @throws PageNotFoundException
     * @throws GoogleCaptchaException
     */
    public function query(GoogleUrlInterface $googleUrl, BrowserInterface $browser = null)
    {

        if ($googleUrl->getResultType() !== GoogleUrl::RESULT_TYPE_ALL) {
            throw new Exception(
                'The requested url is not valid for the google client.'
                . 'Google client only supports general searches. See GoogleUrl::setResultType() for more infos.'
            );
        }

        if (null === $browser) {
            $browser = $this->defaultBrowser;
        }

        if (!$browser) {
            throw new Exception('No browser given for query and no default browser was found');
        }

        $response = $browser->navigateToUrl($googleUrl);

        $statusCode = $response->getHttpResponseStatus();

        $effectiveUrl = GoogleUrlArchive::fromString($response->getEffectiveUrl()->__toString());

        if (200 == $statusCode) {
            return new GoogleSerp($response->getPageContent(), $effectiveUrl);
        } else {
            if (404 == $statusCode) {
                throw new PageNotFoundException($response);
            } else {
                $errorDom = new GoogleError($response->getPageContent(), $effectiveUrl);

                if ($errorDom->isCaptcha()) {
                    throw new GoogleCaptchaException(new GoogleCaptcha($errorDom));
                } else {
                    $failedUrl = $response->getInitialUrl();
                    throw new InvalidResponseException(
                        $response,
                        "The http response from $failedUrl has an invalid status code: '$statusCode'"
                    );
                }
            }
        }
    }
}

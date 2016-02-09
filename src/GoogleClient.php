<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google;

use Serps\Core\Captcha\CaptchaSolverInterface;
use Serps\Core\Http\HttpClientInterface;
use Serps\Core\Http\Proxy;
use Serps\Core\UrlArchive;
use Serps\Exception;
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
     * @var CaptchaSolverInterface|null
     */
    protected $captchaSolver;

    /**
     * @param HttpClientInterface $client
     * @param CaptchaSolverInterface|null $captchaSolver
     */
    public function __construct(HttpClientInterface $client, CaptchaSolverInterface $captchaSolver = null)
    {
        $this->client = $client;
        $this->captchaSolver = $captchaSolver;
    }

    /**
     * @param GoogleUrlInterface $googleUrl
     * @param Proxy|null $proxy
     * @return GoogleDom
     * @throws Exception\CaptchaException
     * @throws Exception\PageNotFoundException
     * @throws Exception\RequestErrorException
     */
    public function query(GoogleUrlInterface $googleUrl, Proxy $proxy = null)
    {
        $request = $googleUrl->buildRequest();
        $response = $this->client->sendRequest($request, $proxy);

        $statusCode = $response->getStatusCode();
        $urlArchive = $googleUrl->getArchive();

        $effectiveUrl = $response->getHeader('X-SERPS-EFFECTIVE-URL');
        if (!count($effectiveUrl) > 0) {
            throw new Exception('Response does not provide a value for the header X-SERPS-EFFECTIVE-URL');
        }

        $effectiveUrl = UrlArchive::fromString($effectiveUrl[0]);

        if (200 == $statusCode) {

            switch ($urlArchive->getResultType()) {
                case GoogleUrl::RESULT_TYPE_ALL:
                    $dom = new GoogleSerp((string)$response->getBody(), $urlArchive, $effectiveUrl, $proxy);
                    break;
                default:
                    $dom = new GoogleDom((string)$response->getBody(), $urlArchive, $effectiveUrl, $proxy);
            }

            return $dom;
        } else {
            if (404 == $statusCode) {
                throw new Exception\PageNotFoundException();
            } else {
                $errorDom = new GoogleError((string)$response->getBody(), $urlArchive, $effectiveUrl, $proxy);

                if ($errorDom->isCaptcha()) {
                    throw new Exception\CaptchaException(new GoogleCaptcha($errorDom));
                } else {
                    throw new Exception\RequestErrorException($errorDom);
                }
            }
        }

    }

    public function solveCaptcha(GoogleCaptcha $captchaPage)
    {
        if (!$this->captchaSolver) {
            throw new Exception('The client does not have a captcha solver.');
        }

        $result = $this->captchaSolver->solve($captchaPage);

        if ($result) {
           // TODO send the captcha to google with the given proxy
        } else {
            throw new Exception('Could not solve captcha');
        }
    }
}

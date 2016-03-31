<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google;

use \InvalidArgumentException;
use Serps\Core\Http\HttpClientInterface;
use Serps\Core\Http\SearchEngineResponse;
use Serps\Core\UrlArchive;
use Serps\Exception\CaptchaException;
use Serps\SearchEngine\Google\Exception\GoogleCaptchaException;
use Serps\SearchEngine\Google\GoogleUrl;
use Serps\SearchEngine\Google\GoogleClient;
use Serps\SearchEngine\Google\GoogleUrlArchive;
use Serps\SearchEngine\Google\Page\GoogleCaptcha;
use Serps\SearchEngine\Google\Page\GoogleSerp;
use Zend\Diactoros\Request;

/**
 * @covers Serps\SearchEngine\Google\GoogleClient
 */
class GoogleClientTest extends \PHPUnit_Framework_TestCase
{

    public function testValidDom()
    {
        $httpClientMock = $this->getMock(HttpClientInterface::class);
        $responseFromMock = new SearchEngineResponse(
            [],
            200,
            file_get_contents('test/resources/pages-evaluated/simpsons+movie+trailer.html'),
            false,
            GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons+movie+trailer'),
            GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons+movie+trailer'),
            null
        );
        $httpClientMock->method('sendRequest')->willReturn($responseFromMock);

        /* @var $httpClientMock HttpClientInterface */
        $googleClient = new GoogleClient($httpClientMock);
        $url = GoogleUrl::fromString('https://www.google.fr/search?q=simpsons+movie+trailer');

        $dom = $googleClient->query($url);
        $this->assertInstanceOf(GoogleSerp::class, $dom);
        $this->assertEquals('https://www.google.fr/search?q=simpsons+movie+trailer', (string)$dom->getEffectiveUrl());
    }

    public function testCaptchaDom()
    {
        $httpClientMock = $this->getMock(HttpClientInterface::class);
        $responseFromMock = new SearchEngineResponse(
            [],
            503,
            file_get_contents('test/resources/pages-evaluated/captcha.html'),
            false,
            GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons+movie+trailer'),
            GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons+movie+trailer'),
            null
        );

        $httpClientMock->method('sendRequest')->willReturn($responseFromMock);

        /* @var $httpClientMock HttpClientInterface */
        $googleClient = new GoogleClient($httpClientMock);
        $url = GoogleUrl::fromString('https://www.google.fr/search?q=simpsons+movie+trailer');

        try {
            $googleClient->query($url);
            $this->fail('Exception not thrown');
        } catch (GoogleCaptchaException $e) {
            $this->assertInstanceOf(GoogleCaptcha::class, $e->getCaptcha());
            $this->assertEquals('128.78.166.25', $e->getCaptcha()->getDetectedIp());
        }
    }

    public function testUserAgentAccessors()
    {
        $googleClient = new GoogleClient($this->getMock(HttpClientInterface::class));
        $googleClient->setUserAgent('test-user-agent');
        $this->assertEquals('test-user-agent', $googleClient->getUserAgent());

        $googleClient->setUserAgent('foo-user-agent');
        $this->assertEquals('foo-user-agent', $googleClient->getUserAgent());

        $googleClient->setUserAgent(null);
        $this->assertEquals(null, $googleClient->getUserAgent());

        $this->setExpectedException(InvalidArgumentException::class);
        $googleClient->setUserAgent(true);
    }

    public function testUserAgentWithOnRequest()
    {
        $httpClientMock = $this->getMock(HttpClientInterface::class);
        $responseFromMock = new SearchEngineResponse(
            [],
            200,
            file_get_contents('test/resources/pages-evaluated/simpsons+movie+trailer.html'),
            false,
            GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons+movie+trailer'),
            GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons+movie+trailer'),
            null
        );
        $httpClientMock->method('sendRequest')->willReturn($responseFromMock);


        /* @var $httpClientMock HttpClientInterface */
        $googleClient = new GoogleClient($httpClientMock);
        $googleClient->setUserAgent('foo-ua');
        $url = GoogleUrl::fromString('https://www.google.fr/search?q=simpsons+movie+trailer');
        $request = $googleClient->prepareRequest($url->buildRequest());

        $this->assertCount(1, $request->getHeader('user-agent'));
        $this->assertEquals('foo-ua', $request->getHeader('user-agent')[0]);


        // Test that it does not overides the user agent if the request has one
        $request = new Request(null, null, 'php://temp', ['user-agent' => 'request-ua']);
        $request = $googleClient->prepareRequest($request);
        $this->assertCount(1, $request->getHeader('user-agent'));
        $this->assertEquals('request-ua', $request->getHeader('user-agent')[0]);
    }
}

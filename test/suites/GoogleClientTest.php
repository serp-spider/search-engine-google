<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google;

use Serps\Core\Http\HttpClientInterface;
use Serps\Exception\CaptchaException;
use Serps\SearchEngine\Google\GoogleUrl;
use Serps\SearchEngine\Google\GoogleClient;
use Serps\SearchEngine\Google\Page\GoogleCaptcha;
use Zend\Diactoros\Response;
use Serps\SearchEngine\Google\Page\GoogleSerp;

/**
 * @covers Serps\SearchEngine\Google\GoogleClient
 */
class GoogleClientTest extends \PHPUnit_Framework_TestCase
{

    public function testValidDom()
    {
        $httpClientMock = $this->getMock(HttpClientInterface::class);
        $responseFromMock = new Response();
        $responseFromMock = $responseFromMock->withHeader('X-SERPS-EFFECTIVE-URL', 'https://www.google.fr/search?q=simpsons+movie+trailer');
        $responseFromMock = $responseFromMock->withHeader('X-SERPS-PROXY', '');
        $responseFromMock->getBody()->write(
            file_get_contents('test/resources/simpsons+movie+trailer.html')
        );
        $httpClientMock->method('sendRequest')->willReturn($responseFromMock);



        $googleClient = new GoogleClient($httpClientMock);
        $url = GoogleUrl::fromString('https://www.google.fr/search?q=simpsons+movie+trailer');

        $dom = $googleClient->query($url);
        $this->assertInstanceOf(GoogleSerp::class, $dom);
        $this->assertEquals('https://www.google.fr/search?q=simpsons+movie+trailer', (string)$dom->getEffectiveUrl());
    }

    public function testCaptchaDom()
    {
        $httpClientMock = $this->getMock(HttpClientInterface::class);
        $responseFromMock = new Response();
        $responseFromMock = $responseFromMock->withStatus(503);
        $responseFromMock = $responseFromMock->withHeader('X-SERPS-EFFECTIVE-URL', 'https://www.google.fr/search?q=simpsons+movie+trailer');
        $responseFromMock = $responseFromMock->withHeader('X-SERPS-PROXY', '');
        $responseFromMock->getBody()->write(
            file_get_contents('test/resources/captcha.html')
        );
        $httpClientMock->method('sendRequest')->willReturn($responseFromMock);

        $googleClient = new GoogleClient($httpClientMock);
        $url = GoogleUrl::fromString('https://www.google.fr/search?q=simpsons+movie+trailer');

        try {
            $googleClient->query($url);
            $this->fail('Exception not thrown');
        } catch (CaptchaException $e) {
            $this->assertInstanceOf(GoogleCaptcha::class, $e->getCaptcha());
            $this->assertEquals('128.78.166.25', $e->getCaptcha()->getDetectedIp());
        }
    }
}

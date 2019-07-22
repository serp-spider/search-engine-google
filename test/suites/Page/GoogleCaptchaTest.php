<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google\Page;

use Serps\SearchEngine\Google\Page\GoogleError;
use Serps\SearchEngine\Google\GoogleUrlArchive;
use Serps\SearchEngine\Google\Page\GoogleCaptcha;

/**
 * @covers Serps\SearchEngine\Google\Page\GoogleCaptcha
 * @covers Serps\SearchEngine\Google\Page\GoogleError::isCaptcha
 */
class GoogleCaptchaTest extends \PHPUnit_Framework_TestCase
{

    public function testCaptcha()
    {

        $url = GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons&hl=en_US');
        $effectiveUrl = GoogleUrlArchive::fromString('https://ipv4.google.com/sorry');
        $errorDom = new GoogleError(file_get_contents('test/resources/pages-evaluated/captcha.html'), $effectiveUrl);

        $this->assertTrue($errorDom->isCaptcha());

        $captchaDom = new GoogleCaptcha($errorDom);

        $this->assertEquals('188.94.206.49', $captchaDom->getDetectedIp());

        $this->assertInstanceOf(GoogleError::class, $captchaDom->getErrorPage());
    }
}

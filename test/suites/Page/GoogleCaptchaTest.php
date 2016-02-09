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
        $errorDom = new GoogleError(file_get_contents('test/resources/captcha.html'), $url, $effectiveUrl);

        $this->assertTrue($errorDom->isCaptcha());

        $captchaDom = new GoogleCaptcha($errorDom);

        $this->assertEquals('128.78.166.25', $captchaDom->getDetectedIp());

        $expected = 'https://ipv4.google.com/sorry/image?id=11933951502432445488&q=CGMSBIBOphkYlPS4tQUiGQDxp4NL98wGI3m9KmgfVcObdGm1KM4Kn_Y&hl=en';
        $this->assertEquals($expected, $captchaDom->getImageUrl());

    }
}

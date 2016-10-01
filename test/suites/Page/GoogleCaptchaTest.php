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

        $this->assertEquals('88.164.16.115', $captchaDom->getDetectedIp());

        $expected = 'https://ipv4.google.com/sorry/image?id=2807240840246447177&q=CGMSBFikEHMYmPy9vwUiGQDxp4NLbaoWhAiT1YLcRhBcqrVhooLaAM4&hl=en&continue=https%3A%2F%2Fwww.google.fr%2Fsearch%3Fsourceid%3Dchrome-psyapi2%26ion%3D1%26espv%3D2%26ie%3DUTF-8%26client%3Dubuntu%26q%3Dsimpsons%26oq%3Dsimpsons%26aqs%3Dchrome..69i57j0l5.1511j0j7';
        $this->assertEquals($expected, $captchaDom->getImageUrl());


        $this->assertEquals('CGMSBFikEHMYmPy9vwUiGQDxp4NLbaoWhAiT1YLcRhBcqrVhooLaAM4', $captchaDom->getId());

        $this->assertInstanceOf(GoogleError::class, $captchaDom->getErrorPage());
    }
}

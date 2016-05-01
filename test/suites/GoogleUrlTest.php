<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google;

use Psr\Http\Message\RequestInterface;
use Serps\SearchEngine\Google\GoogleUrl;
use Serps\SearchEngine\Google\GoogleUrlArchive;

/**
 * @covers Serps\SearchEngine\Google\GoogleUrl
 * @covers Serps\SearchEngine\Google\GoogleUrlArchive
 * @covers Serps\SearchEngine\Google\GoogleUrlTrait
 */
class GoogleUrlTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $googleUrl = new GoogleUrl();
        $this->assertEquals('google.com', $googleUrl->getHost());
        $this->assertEquals('/search', $googleUrl->getPath());
    }

    public function testGetArchive()
    {
        $googleUrl = GoogleUrl::fromString('https://google.com/search?q=simpsons');
        $this->assertInstanceOf(GoogleUrlArchive::class, $googleUrl->getArchive());

        $this->assertEquals('https://google.com/search?q=simpsons', $googleUrl->getArchive()->buildUrl());
    }

    public function testLanguageRestriction()
    {
        $googleUrl = GoogleUrl::fromString('https://google.com/search?q=simpsons');
        $this->assertEquals(null, $googleUrl->getLanguageRestriction());

        $googleUrl->setLanguageRestriction('de');
        $this->assertEquals('lang_de', $googleUrl->getLanguageRestriction());
        $this->assertEquals('lang_de', $googleUrl->getParamValue('lr'));

        $googleUrl->setLanguageRestriction('lang_fr');
        $this->assertEquals('lang_fr', $googleUrl->getLanguageRestriction());
        $this->assertEquals('lang_fr', $googleUrl->getParamValue('lr'));
    }

    public function testPage()
    {
        $googleUrl = GoogleUrl::fromString('https://google.com/search?q=simpsons');
        $this->assertEquals(1, $googleUrl->getPage());

        $googleUrl->setPage(1);
        $this->assertEquals(1, $googleUrl->getPage());
        $this->assertFalse($googleUrl->hasParam('start'));

        $googleUrl->setPage(2);
        $this->assertEquals(2, $googleUrl->getPage());
        $this->assertEquals(10, $googleUrl->getParamValue('start'));

        $googleUrl->setPage(0);
        $this->assertEquals(1, $googleUrl->getPage());
        $this->assertFalse($googleUrl->hasParam('start'));
    }

    public function testResultsPerPage()
    {
        $googleUrl = GoogleUrl::fromString('https://google.com/search?q=simpsons');
        $this->assertEquals(10, $googleUrl->getResultsPerPage());

        $googleUrl->setPage(2);
        $googleUrl->setResultsPerPage(20);
        $this->assertEquals(20, $googleUrl->getResultsPerPage());
        $this->assertEquals(20, $googleUrl->getParamValue('num'));
        $this->assertEquals(2, $googleUrl->getPage());

        // special cases: more than 100 or less than  1
        $googleUrl->setResultsPerPage(200);
        $this->assertEquals(100, $googleUrl->getResultsPerPage());
        $googleUrl->setResultsPerPage(0);
        $this->assertEquals(1, $googleUrl->getResultsPerPage());

        //reset to default
        $googleUrl->setResultsPerPage(10);
        $this->assertFalse($googleUrl->hasParam('num'));
    }

    public function testSearchTerm()
    {
        $googleUrl = GoogleUrl::fromString('https://google.com/search?q=simpsons');
        $this->assertEquals('simpsons', $googleUrl->getSearchTerm());

        $googleUrl->setSearchTerm('bart simpsons');
        $this->assertEquals('bart simpsons', $googleUrl->getSearchTerm());
        $this->assertEquals('bart+simpsons', $googleUrl->getParamValue('q'));

    }

    public function testResultType()
    {
        $googleUrl = GoogleUrl::fromString('https://google.com/search?q=simpsons');
        $this->assertEquals(GoogleUrl::RESULT_TYPE_ALL, $googleUrl->getResultType());

        $googleUrl->setResultType(GoogleUrl::RESULT_TYPE_IMAGES);
        $this->assertEquals(GoogleUrl::RESULT_TYPE_IMAGES, $googleUrl->getResultType());
        $this->assertEquals(GoogleUrl::RESULT_TYPE_IMAGES, $googleUrl->getParamValue('tbm'));

        $googleUrl->setResultType(GoogleUrl::RESULT_TYPE_ALL);
        $this->assertFalse($googleUrl->hasParam('tbm'));
    }
}

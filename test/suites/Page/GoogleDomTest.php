<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google\Page;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\GoogleUrlArchive;

/**
 * @covers Serps\SearchEngine\Google\Page\GoogleDom
 */
class GoogleDomTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return \Serps\SearchEngine\Google\Page\GoogleDom
     */
    public function getDom()
    {
        $url = GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons&hl=en_US');
        return new GoogleDom(file_get_contents('test/resources/simpsons.html'), $url, $url);
    }

    public function testGetXPath()
    {
        $xpath = $this->getDom()->getXpath();
        $this->assertInstanceOf(\DOMXPath::class, $xpath);
    }



    public function testBuildUrl()
    {
        $dom = $this->getDom();
        $this->assertInstanceOf(GoogleUrlArchive::class, $dom->getUrl());
        $this->assertEquals('https://www.google.fr/search?q=simpsons&hl=en_US', $dom->getUrl()->buildUrl());
    }

    public function testGetDom()
    {
        $googleDom = $this->getDom();

        $this->assertInstanceOf(\DOMDocument::class, $googleDom->getDom());

    }
}

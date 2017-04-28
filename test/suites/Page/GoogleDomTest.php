<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google\Page;

use Serps\Core\Http\Proxy;
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
        return new GoogleDom(file_get_contents('test/resources/simple-dom.html'), $url);
    }

    public function testGetXPath()
    {
        $xpath = $this->getDom()->getXpath();
        $this->assertInstanceOf(\DOMXPath::class, $xpath);
    }

    public function testGetDom()
    {
        $googleDom = $this->getDom();
        $this->assertInstanceOf(\DOMDocument::class, $googleDom->getDom());
    }

    public function testXPathQuery()
    {
        $dom = $this->getDom();

        $fooSpan = $dom->xpathQuery('descendant::div[@class="baz"]/span[@class="foo"]');

        $this->assertEquals(1, $fooSpan->length);
        $this->assertEquals('<span class="foo">baz - foo span</span>', $fooSpan->item(0)->C14N());

        $fooSpan = $dom->xpathQuery('descendant::div[@class="baz"]');
        $fooSpan = $dom->xpathQuery('span[@class="foo"]', $fooSpan->item(0));

        $this->assertEquals(1, $fooSpan->length);
        $this->assertEquals('<span class="foo">baz - foo span</span>', $fooSpan->item(0)->C14N());

        $fooSpan = $dom->xpathQuery('span[@class="foo"]', $fooSpan->item(0));
        $this->assertEquals(0, $fooSpan->length);
    }


    public function testCssQuery()
    {
        $dom = $this->getDom();

        $fooSpan = $dom->cssQuery('.foo');
        $this->assertEquals(3, $fooSpan->length);

        $fooSpan = $dom->cssQuery('.foo', $dom->cssQuery('.baz')->item(0));
        $this->assertEquals(1, $fooSpan->length);
        $this->assertEquals('<span class="foo">baz - foo span</span>', $fooSpan->item(0)->C14N());
    }

    public function testGetUrl()
    {
        $googleDom = $this->getDom();
        $this->assertSame('https://www.google.fr/search?q=simpsons&hl=en_US', $googleDom->getUrl()->buildUrl());
    }

    public function testGetJsonNodeProperty()
    {
        $url = GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons&hl=en_US');
        $domString = '<html>
            <div id="foo1">"{"foo":"bar", "bar":"baz"}"</div>
            <div id="foo2">"{"foo":"qux", "baz":"boo"}"</div>
            <div id="foo3">"":"boo"}"</div>
        </html>';

        $dom = new GoogleDom($domString, $url);

        $nodeFoo1 = $dom->cssQuery('#foo1')->item(0);
        $nodeFoo2 = $dom->cssQuery('#foo2')->item(0);
        $nodeFoo3 = $dom->cssQuery('#foo3')->item(0);

        $this->assertEquals('bar', $dom->getJsonNodeProperty('foo', $nodeFoo1));
        $this->assertEquals('qux', $dom->getJsonNodeProperty('foo', $nodeFoo2));

        $this->assertEquals('baz', $dom->getJsonNodeProperty('bar', $nodeFoo1));
        $this->assertEquals(null, $dom->getJsonNodeProperty('bar', $nodeFoo2));

        $this->assertEquals(null, $dom->getJsonNodeProperty('baz', $nodeFoo1));
        $this->assertEquals('boo', $dom->getJsonNodeProperty('baz', $nodeFoo2));

        $this->assertEquals(null, $dom->getJsonNodeProperty('baz', $nodeFoo3));
    }
}

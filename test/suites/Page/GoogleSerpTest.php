<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google\Page;

use Serps\Core\Serp\CompositeResultSet;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleSerp;
use Serps\SearchEngine\Google\GoogleUrlArchive;

/**
 * @covers Serps\SearchEngine\Google\Page\GoogleSerp
 */
class GoogleSerpTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return GoogleSerp
     */
    public function getDomJavascript()
    {
        $url = GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons&hl=en_US');
        return new GoogleSerp(file_get_contents('test/resources/pages-evaluated/simpsons.html'), $url);
    }
    /**
     * @return GoogleSerp
     */
    public function getDomNoJavascript()
    {
        $url = GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons&hl=en_US');
        return new GoogleSerp(file_get_contents('test/resources/pages-raw/simpsons.html'), $url);
    }

    public function testGetNumberOfResults()
    {
        $count = $this->getDomJavascript()->getNumberOfResults();
        $this->assertEquals(65200000, $count);

        $count = $this->getDomNoJavascript()->getNumberOfResults();
        $this->assertEquals(70900000, $count);
    }


    public function testGetLocation()
    {
        $this->assertEquals('Nantes', $this->getDomJavascript()->getLocation());
    }

    public function testGetNaturalResults()
    {
        $dom = $this->getDomJavascript();

        $results = $dom->getNaturalResults();

        $this->assertInstanceOf(IndexedResultSet::class, $results);
        $this->assertCount(10, $results);


        $dom = $this->getDomNoJavascript();

        $results = $dom->getNaturalResults();

        $this->assertInstanceOf(IndexedResultSet::class, $results);
        $this->assertCount(10, $results);
    }

    public function testGetAdwordsResults()
    {
        $dom = $this->getDomJavascript();
        $results = $dom->getAdwordsResults();
        $this->assertInstanceOf(CompositeResultSet::class, $results);

        $dom = $this->getDomNoJavascript();
        $results = $dom->getAdwordsResults();
        $this->assertInstanceOf(CompositeResultSet::class, $results);
    }

    public function testJavascriptEvaluated()
    {
        $this->assertTrue($this->getDomJavascript()->javascriptIsEvaluated());
        $this->assertFalse($this->getDomNoJavascript()->javascriptIsEvaluated());
    }

    public function testRelatedSearches()
    {
        $gUrl = GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons+related');
        $dom = new GoogleSerp(file_get_contents('test/resources/pages-evaluated/simpsons(related).html'), $gUrl);

        $rs = $dom->getRelatedSearches();

        $this->assertCount(8, $rs);

        $this->assertEquals((array)$rs[0], [
            'title' => 'simpsons watch online',
            'url' => 'https://www.google.fr/search?client=ubuntu&hs=mPo&biw=1920&bih=992&q=simpsons+watch+online&revid=1278607378&sa=X&ved=0ahUKEwisvuHvz4vNAhVDI8AKHXh5AKAQ1QIIfygA'
        ]);

        $this->assertEquals((array)$rs[1], [
            'title' => 'simpsons tv',
            'url' => 'https://www.google.fr/search?client=ubuntu&hs=mPo&biw=1920&bih=992&q=simpsons+tv&revid=1278607378&sa=X&ved=0ahUKEwisvuHvz4vNAhVDI8AKHXh5AKAQ1QIIgAEoAQ'
        ]);

        $this->assertEquals((array)$rs[4], [
            'title' => 'the simpsons episode 1',
            'url' => 'https://www.google.fr/search?client=ubuntu&hs=mPo&biw=1920&bih=992&q=the+simpsons+episode+1&revid=1278607378&sa=X&ved=0ahUKEwisvuHvz4vNAhVDI8AKHXh5AKAQ1QIIgwEoBA'
        ]);

        $this->assertEquals((array)$rs[7], [
            'title' => 'the simpsons barthood',
            'url' => 'https://www.google.fr/search?client=ubuntu&hs=mPo&biw=1920&bih=992&q=the+simpsons+barthood&revid=1278607378&sa=X&ved=0ahUKEwisvuHvz4vNAhVDI8AKHXh5AKAQ1QIIhgEoBw'
        ]);
    }
}

<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google\Page;

use Serps\Core\Serp\ResultSet;
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
    public function getDom()
    {
        $url = GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons&hl=en_US');
        return new GoogleSerp(file_get_contents('test/resources/simpsons.html'), $url, $url);
    }

    public function testGetLocation()
    {
        $this->assertEquals('Nantes', $this->getDom()->getLocation());
    }

    public function testGetNaturalResults()
    {
        $dom = $this->getDom();

        $results = $dom->getNaturalResults();

        $this->assertInstanceOf(ResultSet::class, $results);
        $this->assertCount(10, $results);
    }
}

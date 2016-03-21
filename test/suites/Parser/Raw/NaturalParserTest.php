<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google\Parser\Raw;

use Serps\SearchEngine\Google\Parser\Raw\NaturalParser;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\GoogleUrlArchive;
use Serps\Core\Serp\ResultSet;
use Serps\SearchEngine\Google\NaturalResultType;

/**
 * Testing parser is hard, because it relies on google pages
 *
 * The tests here are parsing a saved html version of a google page.
 * They do not prevent google from changing its dom. If it
 * happens the saved html and the following tests must be updated.
 *
 * When the tests are updated, make sure that the new one include the same kind of results.
 * For instance if the previous test included a ``inDepthArticle`` the new test should do so.
 *
 *
 * @covers Serps\SearchEngine\Google\Parser\AbstractNaturalParser
 * @covers Serps\SearchEngine\Google\Parser\Raw\NaturalParser
 * @covers Serps\SearchEngine\Google\Parser\Raw\Rule\ClassicalResult
 * @covers Serps\SearchEngine\Google\Parser\Raw\Rule\ClassicalLargeVideo
 * @covers Serps\SearchEngine\Google\Parser\Raw\Rule\ClassicalThumbVideo
 * @covers Serps\SearchEngine\Google\Parser\Raw\Rule\Map
 * @covers Serps\SearchEngine\Google\Parser\Raw\Rule\ImageGroup
 */
class NaturalParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @codeCoverageIgnore
     */
    public function testParserRawNatural()
    {

        $gUrl = GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons&hl=en_US');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-raw/simpsons.html'), $gUrl, $gUrl);

        $naturalParser = new  NaturalParser();
        $result = $naturalParser->parse($dom);

        $types = [];
        foreach ($result->getItems() as $item) {
            $types[] = $item->getTypes()[0];
        }



        $this->assertInstanceOf(ResultSet::class, $result);
        $this->assertCount(10, $result);
        $this->assertEquals([
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::IMAGE_GROUP,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL
        ], $types);

    }

    public function testParserWithVideo()
    {

        $gUrl = GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons+movie+trailer');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-raw/simpsons+movie+trailer.html'), $gUrl, $gUrl);

        $naturalParser = new  NaturalParser();
        $results = $naturalParser->parse($dom);

        $types = [];
        foreach ($results->getItems() as $item) {
            $types[] = $item->getTypes()[0];
        }

        $this->assertInstanceOf(ResultSet::class, $results);
        $this->assertCount(10, $results);
        $this->assertEquals([
            NaturalResultType::CLASSICAL_VIDEO,
            NaturalResultType::CLASSICAL_VIDEO,
            NaturalResultType::CLASSICAL_VIDEO,
            NaturalResultType::CLASSICAL_VIDEO,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL_VIDEO,
            NaturalResultType::CLASSICAL_VIDEO,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL

        ], $types);

        $this->assertTrue($results->getItems()[0]->getDataValue('videoLarge'));
    }


    public function testResultWithMap()
    {

        $gUrl = GoogleUrlArchive::fromString('https://www.google.fr/search?q=shop+near+paris');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-raw/shop-near-paris.html'), $gUrl, $gUrl);

        $naturalParser = new  NaturalParser();
        $result = $naturalParser->parse($dom);

        $types = [];
        foreach ($result->getItems() as $item) {
            $types[] = $item->getTypes()[0];
        }

        $this->assertInstanceOf(ResultSet::class, $result);
        $this->assertCount(11, $result);
        $this->assertEquals([
            NaturalResultType::MAP,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::IMAGE_GROUP,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL
        ], $types);

    }
}

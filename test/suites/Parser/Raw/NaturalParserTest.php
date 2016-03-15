<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google\Parser\Raw;

use Serps\SearchEngine\Google\Parser\Raw\NaturalParser;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\GoogleUrlArchive;
use Serps\Core\Serp\ResultSet;
use Serps\SearchEngine\Google\Parser\ResultType;

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
 * @covers Serps\SearchEngine\Google\Parser\Raw\NaturalParser
 * @covers Serps\SearchEngine\Google\Parser\Raw\Rule\ClassicalResult
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
            $types[] = $item->getType();
        }



        $this->assertInstanceOf(ResultSet::class, $result);
        $this->assertCount(10, $result);
        $this->assertEquals([
            ResultType::CLASSICAL,
            ResultType::CLASSICAL,
            ResultType::IMAGE_GROUP,
            ResultType::CLASSICAL,
            ResultType::CLASSICAL,
            ResultType::CLASSICAL,
            ResultType::CLASSICAL,
            ResultType::CLASSICAL,
            ResultType::CLASSICAL,
            ResultType::CLASSICAL
        ], $types);

    }


    public function testResultWithMap()
    {

        $gUrl = GoogleUrlArchive::fromString('https://www.google.fr/search?q=shop+near+paris');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-raw/shop-near-paris.html'), $gUrl, $gUrl);

        $naturalParser = new  NaturalParser();
        $result = $naturalParser->parse($dom);

        $types = [];
        foreach ($result->getItems() as $item) {
            $types[] = $item->getType();
        }

        $this->assertInstanceOf(ResultSet::class, $result);
        $this->assertCount(11, $result);
        $this->assertEquals([
            ResultType::MAP,
            ResultType::CLASSICAL,
            ResultType::CLASSICAL,
            ResultType::CLASSICAL,
            ResultType::CLASSICAL,
            ResultType::IMAGE_GROUP,
            ResultType::CLASSICAL,
            ResultType::CLASSICAL,
            ResultType::CLASSICAL,
            ResultType::CLASSICAL,
            ResultType::CLASSICAL
        ], $types);

    }
}

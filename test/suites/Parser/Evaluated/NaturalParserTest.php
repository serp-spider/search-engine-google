<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\Parser\Evaluated\NaturalParser;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\GoogleUrlArchive;
use Serps\Core\Serp\IndexedResultSet;
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
 * @covers Serps\SearchEngine\Google\Parser\AbstractParser
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\NaturalParser
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ClassicalResult
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SearchResultGroup
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TweetsCarousel
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\InTheNews
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Divider
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ImageGroup
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ClassicalWithLargeVideo
 */
class NaturalParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParserNatural()
    {

        $gUrl = GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons&hl=en_US');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/simpsons.html'), $gUrl, $gUrl);

        $naturalParser = new  NaturalParser();
        $result = $naturalParser->parse($dom);

        $types = [];
        foreach ($result->getItems() as $item) {
            $types[] = $item->getTypes()[0];
        }

        $this->assertInstanceOf(IndexedResultSet::class, $result);
        $this->assertCount(10, $result);
        $this->assertEquals([
            NaturalResultType::CLASSICAL,
            NaturalResultType::TWEETS_CAROUSEL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::IN_THE_NEWS,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
        ], $types);


        // Test in the news
        $inTheNews = $result->getResultsByType('inTheNews');
        $this->assertEquals(3, $inTheNews[0]->getRealPosition());
        $this->assertEquals(
            "'The Simpsons': Greatest Political Moments",
            $inTheNews[0]->getDataValue('cards')[0]->getDataValue('title')
        );
        $this->assertEquals(
            'http://www.rollingstone.com/politics/news/the-simpsons-greatest-political-moments-20160323',
            $inTheNews[0]->getDataValue('cards')[0]->getDataValue('url')
        );
        $this->assertEquals(
            "'The Simpsons' has lampooned political figures over four presidential administrations and ...",
            $inTheNews[0]->getDataValue('cards')[0]->getDataValue('description')
        );


        // Test twitter tweet carousel
        $this->assertEquals('@TheSimpsons', $result->getItems()[1]->getDataValue('user'));
        $this->assertEquals(
            'https://twitter.com/TheSimpsons?ref_src=twsrc%5Egoogle%7Ctwcamp%5Eserp%7Ctwgr%5Eauthor',
            $result->getItems()[1]->getDataValue('url')
        );
        $this->assertEquals(
            'The Simpsons (@TheSimpsons) | Twitter',
            $result->getItems()[1]->getDataValue('title')
        );
    }

    public function testParserWithImageGroup()
    {

        $gUrl = GoogleUrlArchive::fromString('https://www.google.com.au/search?q=simpsons+donut');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/simpsons+donut.html'), $gUrl, $gUrl);

        $naturalParser = new  \Serps\SearchEngine\Google\Parser\Evaluated\NaturalParser();
        $result = $naturalParser->parse($dom);

        $types = [];
        foreach ($result->getItems() as $item) {
            $types[] = $item->getTypes()[0];
        }


        $this->assertInstanceOf(IndexedResultSet::class, $result);
        $this->assertCount(8, $result);
        $this->assertEquals([
            NaturalResultType::IMAGE_GROUP,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL_VIDEO,
            NaturalResultType::CLASSICAL_VIDEO,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL
        ], $types);

        $this->assertCount(12, $result->getItems()[0]->getDataValue('images'));
        $this->assertEquals(
            'http://superawesomevectors.com/free-vector-donut-drawing/',
            $result->getItems()[0]->getDataValue('images')[0]->getDataValue('sourceUrl')
        );
        $this->assertEquals(
            'https://www.google.com.au/search?q=simpsons+donut&tbm=isch&imgil=PsgymH70iPP7VM%253A%253BeaF3My1vToZseM%253Bhttp%25253A%25252F%25252Fsuperawesomevectors.com%25252Ffree-vector-donut-drawing%25252F&source=iu&pf=m&fir=PsgymH70iPP7VM%253A%252CeaF3My1vToZseM%252C_&usg=___xAQ2PmWuTZdcZq_-t7ELD0Maqw%3D',
            $result->getItems()[0]->getDataValue('images')[0]->getDataValue('targetUrl')
        );
        $this->assertEquals(
            'https://www.google.com.au/search?q=simpsons+donut&tbm=isch&tbo=u&source=univ&sa=X&ved=0ahUKEwiyo7ucrtvKAhUJHxoKHZBFAHYQsAQIGw',
            $result->getItems()[0]->getDataValue('moreUrl')
        );
    }

    public function testParserWithVideo()
    {

        $gUrl = GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons+movie+trailer');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/simpsons+movie+trailer.html'), $gUrl, $gUrl);

        $naturalParser = new NaturalParser();
        $results = $naturalParser->parse($dom);

        $types = [];
        foreach ($results->getItems() as $item) {
            $types[] = $item->getTypes()[0];
        }

        $this->assertInstanceOf(IndexedResultSet::class, $results);
        $this->assertCount(10, $results);
        $this->assertEquals([
            NaturalResultType::CLASSICAL_VIDEO,
            NaturalResultType::CLASSICAL_VIDEO,
            NaturalResultType::CLASSICAL_VIDEO,
            NaturalResultType::CLASSICAL_VIDEO,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL_VIDEO,
            NaturalResultType::CLASSICAL_VIDEO,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL

        ], $types);

        $this->assertTrue($results->getItems()[0]->getDataValue('videoLarge'));
    }

    public function testResultWithMap()
    {

        $gUrl = GoogleUrlArchive::fromString('https://www.google.fr/search?q=shop+near+paris');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/shop-near-paris.html'), $gUrl, $gUrl);

        $naturalParser = new NaturalParser();
        $result = $naturalParser->parse($dom);

        $types = [];
        foreach ($result->getItems() as $item) {
            $types[] = $item->getTypes()[0];
        }

        $this->assertInstanceOf(IndexedResultSet::class, $result);
        $this->assertCount(11, $result);
        $this->assertEquals([
            NaturalResultType::MAP,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::IMAGE_GROUP
        ], $types);


        $map = $result->getItems()[0];

        $this->assertEquals(
            'https://www.google.fr/search?q=shop+near+paris&npsic=0&rflfq=1&rlha=0&tbm=lcl&sa=X&ved=0ahUKEwjj9PCdqMDLAhUH0xoKHelvDxcQjGoIOw',
            $map->getDataValue('mapUrl')
        );

        $this->assertCount(3, $map->getDataValue('localPack'));
        $this->assertEquals('Bicycle Store', $map->getDataValue('localPack')[0]->getDataValue('title'));
        $this->assertEquals('http://www.bicyclestore.fr/', $map->getDataValue('localPack')[0]->getDataValue('url'));
        $this->assertEquals('17 Boulevard du Temple', $map->getDataValue('localPack')[0]->getDataValue('street'));

        $this->assertEquals('4.0', $map->getDataValue('localPack')[0]->getDataValue('stars'));
        $this->assertEquals(null, $map->getDataValue('localPack')[1]->getDataValue('stars'));


        $this->assertEquals(null, $map->getDataValue('localPack')[0]->getDataValue('review'));
        $this->assertEquals('No reviews', $map->getDataValue('localPack')[1]->getDataValue('review'));
        $this->assertEquals(null, $map->getDataValue('localPack')[2]->getDataValue('review'));

    }
}

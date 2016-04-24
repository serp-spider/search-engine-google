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
 * For instance if the previous test included a ``TweetsCarousel`` the new test should do so.
 *
 *
 * @covers Serps\SearchEngine\Google\Parser\AbstractParser
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\NaturalParser
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ClassicalResult
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\LargeClassicalResult
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SearchResultGroup
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TweetsCarousel
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\InTheNews
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Divider
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ImageGroup
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ClassicalWithLargeVideo
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Map
 */
class NaturalParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParserNatural()
    {

        $gUrl = GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons&hl=en_US');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/simpsons.html'), $gUrl);

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
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/simpsons+donut.html'), $gUrl);

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

        $this->assertCount(12, $result->getItems()[0]->images);
        $this->assertEquals(
            'http://superawesomevectors.com/free-vector-donut-drawing/',
            $result->getItems()[0]->getDataValue('images')[0]->sourceUrl
        );
        $this->assertEquals(
            'https://www.google.com.au/search?q=simpsons+donut&tbm=isch&imgil=PsgymH70iPP7VM%253A%253BeaF3My1vToZseM%253Bhttp%25253A%25252F%25252Fsuperawesomevectors.com%25252Ffree-vector-donut-drawing%25252F&source=iu&pf=m&fir=PsgymH70iPP7VM%253A%252CeaF3My1vToZseM%252C_&usg=___xAQ2PmWuTZdcZq_-t7ELD0Maqw%3D',
            $result->getItems()[0]->getDataValue('images')[0]->targetUrl
        );
        $this->assertEquals(
            'https://www.google.com.au/search?q=simpsons+donut&tbm=isch&tbo=u&source=univ&sa=X&ved=0ahUKEwiyo7ucrtvKAhUJHxoKHZBFAHYQsAQIGw',
            $result->getItems()[0]->moreUrl
        );
    }

    public function testParserWithVideo()
    {

        $gUrl = GoogleUrlArchive::fromString('https://www.google.fr/search?q=simpsons+movie+trailer');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/simpsons+movie+trailer.html'), $gUrl);

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
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/shop-near-paris.html'), $gUrl);

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
            NaturalResultType::IMAGE_GROUP,
            NaturalResultType::CLASSICAL
        ], $types);


        // Test MAP item
        $map = $result->getItems()[0];

        $this->assertEquals(
            'https://www.google.fr/search?q=shop+near+paris&npsic=0&rflfq=1&rlha=0&rllag=48857610,2368833,3837&tbm=lcl&sa=X&ved=0ahUKEwjnofTgoKfMAhVIEpoKHd0eDlkQjGoIOA',
            (string)$map->getDataValue('mapUrl')
        );
        $this->assertCount(3, $map->localPack);
        $this->assertEquals('Paris Store', $map->localPack[0]->title);
        $this->assertequals('http://www.paris-store.com/', $map->localPack[0]->url);
        $this->assertEquals('44 Avenue d\'Ivry', $map->localPack[0]->street);
        // Stars
        $this->assertEquals('4,0', $map->localPack[0]->stars);
        $this->assertEquals(null, $map->localPack[1]->stars);
        // Review
        $this->assertEquals(null, $map->localPack[0]->review);
        $this->assertEquals('Aucun avis', $map->localPack[1]->review);
        $this->assertEquals(null, $map->localPack[2]->review);
        // Phone
        $this->assertEquals('01 44 06 88 18', $map->localPack[0]->phone);
        $this->assertEquals('01 42 06 98 44', $map->localPack[1]->phone);
        $this->assertEquals('01 44 72 88 88', $map->localPack[2]->phone);
    }

    public function testLargeResult()
    {
        $gUrl = GoogleUrlArchive::fromString('https://www.google.fr/search?q=github');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/github.html'), $gUrl);

        $naturalParser = new NaturalParser();
        $result = $naturalParser->parse($dom);

        $types = [];
        foreach ($result->getItems() as $item) {
            $types[] = $item->getTypes()[0];
        }

        $this->assertInstanceOf(IndexedResultSet::class, $result);
        $this->assertCount(7, $result);
        $this->assertEquals([
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::IN_THE_NEWS,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
        ], $types);

        $itemLarge = $result->getItems()[0];
        $this->assertEquals([NaturalResultType::CLASSICAL, NaturalResultType::CLASSICAL_LARGE], $itemLarge->getTypes());

    }

    public function testNidGroup()
    {
        // Some result group are wrapped into an element with a div that has the class "_NId".
        // right now it only showed up on some google.es serp
        $gUrl = GoogleUrlArchive::fromString('https://www.google.es/search?q=alarmas+para+casa&lr=lang_es');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/alarmas+para+casa.html'), $gUrl);

        $naturalParser = new NaturalParser();
        $result = $naturalParser->parse($dom);

        $types = [];
        foreach ($result->getItems() as $item) {
            $types[] = $item->getTypes()[0];
        }

        $this->assertInstanceOf(IndexedResultSet::class, $result);
        $this->assertCount(10, $result);
        $this->assertEquals([
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL
        ], $types);

        $item0 = $result->getItems()[0];
        $this->assertEquals([NaturalResultType::CLASSICAL], $item0->getTypes());
        $this->assertEquals('Alarmas para Hogar - Securitas Direct', $item0->title);
    }
}

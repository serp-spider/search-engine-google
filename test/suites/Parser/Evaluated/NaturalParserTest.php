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
 * Testing parser is hard because it relies on google pages
 *
 * The tests bellow parse a saved html version of a google page.
 * They do not prevent google from changing its dom.
 * If it happens the saved html and the following tests must be updated.
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
        $inTheNews = $result->getResultsByType(NaturalResultType::IN_THE_NEWS);
        $this->assertEquals(4, $inTheNews[0]->getRealPosition());
        $this->assertEquals(
            "'The Simpsons': Greatest Political Moments",
            $inTheNews[0]->getDataValue('news')[0]->getDataValue('title')
        );
        $this->assertEquals(
            'http://www.rollingstone.com/politics/news/the-simpsons-greatest-political-moments-20160323',
            $inTheNews[0]->getDataValue('news')[0]->getDataValue('url')
        );
        $this->assertEquals(
            "'The Simpsons' has lampooned political figures over four presidential administrations and ...",
            $inTheNews[0]->getDataValue('news')[0]->getDataValue('description')
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
            NaturalResultType::CLASSICAL_VIDEO, // TODO change as classical (recipe)
            NaturalResultType::CLASSICAL_VIDEO,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL
        ], $types);

        $this->assertCount(12, $result->getItems()[0]->images);
        $this->assertEquals(
            'https://www.pinterest.com/tailaurindo/simpson/',
            $result->getItems()[0]->getDataValue('images')[0]->sourceUrl
        );
        $this->assertEquals(
            'https://www.google.com.au/search?q=simpsons+donut&tbm=isch&imgil=xo4ZbYgvwQiXxM%253A%253BesZLHUJ3kmTgyM%253Bhttps%25253A%25252F%25252Fwww.pinterest.com%25252Ftailaurindo%25252Fsimpson%25252F&source=iu&pf=m&fir=xo4ZbYgvwQiXxM%253A%252CesZLHUJ3kmTgyM%252C_&usg=__Z-32x0kYL_G1X_tyz88rdtHi_D0%3D',
            $result->getItems()[0]->getDataValue('images')[0]->targetUrl
        );
        $this->assertEquals(
            'https://www.google.com.au/search?q=simpsons+donut&tbm=isch&tbo=u&source=univ&sa=X&ved=0ahUKEwi25M6i44PNAhXEWBoKHVSRBGkQsAQIGw',
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

        $sitelinks = $itemLarge->sitelinks;
        $this->assertCount(6, $sitelinks);

        $this->assertEquals('GitHub Pages', $sitelinks[1]->title);
        $this->assertEquals('GitHub Pages ... Hosted directly from your GitHub repository ...', $sitelinks[1]->description);
        $this->assertEquals('https://pages.github.com/', $sitelinks[1]->url);

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

    public function testFlights()
    {

        $gUrl = GoogleUrlArchive::fromString('https://www.google.fr/search?q=flights&oq=flights');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/flights.html'), $gUrl);

        $naturalParser = new NaturalParser();
        $result = $naturalParser->parse($dom);

        $types = [];
        foreach ($result->getItems() as $item) {
            $types[] = $item->getTypes()[0];
        }

        $this->assertInstanceOf(IndexedResultSet::class, $result);
        $this->assertCount(11, $result);
        $this->assertEquals([
            NaturalResultType::FLIGHTS,
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
    }

    public function testAnswerBox()
    {

        $gUrl = GoogleUrlArchive::fromString('https://www.google.co.uk/search?q=how+is+homer+simpsons&lr=lang_en&hl=en');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/how+is+homer+simpsons.html'), $gUrl);

        $naturalParser = new NaturalParser();
        $result = $naturalParser->parse($dom);

        $types = [];
        foreach ($result->getItems() as $item) {
            $types[] = $item->getTypes()[0];
        }

        $this->assertInstanceOf(IndexedResultSet::class, $result);
        $this->assertCount(11, $result);
        $this->assertEquals([
            NaturalResultType::ANSWER_BOX,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::IN_THE_NEWS,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL,
            NaturalResultType::CLASSICAL
        ], $types);

        $answerBox = $result->getItems()[0];

        $expectedDescription = 'Homer Jay Simpson is the protagonist of the American animated television series The Simpsons as the patriarch of the eponymous family. He is voiced by Dan Castellaneta and first appeared on television, along with the rest of his family, in The Tracey Ullman Show short "Good Night" on April 19, 1987.';
        $this->assertEquals($expectedDescription, $answerBox->description);
        $this->assertEquals('Homer Simpson - Wikipedia, the free encyclopedia', $answerBox->title);
        $this->assertEquals('https://en.wikipedia.org/wiki/Homer_Simpson', $answerBox->destination);
        $this->assertEquals('https://en.wikipedia.org/wiki/Homer_Simpson', $answerBox->url);
    }

    /**
     * spotted by #21 https://github.com/serp-spider/search-engine-google/pull/21
     */
    public function testResultPosition()
    {

        // Page 1
        $gUrl = GoogleUrlArchive::fromString('https://www.google.co.uk/search?q=how+is+homer+simpsons&lr=lang_en&hl=en');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/how+is+homer+simpsons.html'), $gUrl);

        $naturalParser = new NaturalParser();
        $results = $naturalParser->parse($dom);

        $this->assertCount(11, $results);
        $this->assertEquals(1, $results[0]->getRealPosition());
        $this->assertEquals(1, $results[0]->getOnPagePosition());

        // Page 2
        $gUrl = GoogleUrlArchive::fromString('https://www.google.co.uk/search?q=how+is+homer+simpsons&lr=lang_en&hl=en&start=10');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/how+is+homer+simpsons.html'), $gUrl);

        $naturalParser = new NaturalParser();
        $results = $naturalParser->parse($dom);

        $this->assertCount(11, $results);
        $this->assertEquals(11, $results[0]->getRealPosition());
        $this->assertEquals(1, $results[0]->getOnPagePosition());
    }
}

<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\AdwordsResultType;
use Serps\SearchEngine\Google\Page\GoogleSerp;
use Serps\SearchEngine\Google\Parser\Evaluated\NaturalParser;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\GoogleUrlArchive;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\Test\SearchEngine\Google\GoogleSerpTestCase;
use Symfony\Component\Yaml\Yaml;

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
 * @covers \Serps\SearchEngine\Google\Page\GoogleSerp
 * @covers \Serps\SearchEngine\Google\Parser\AbstractParser
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\NaturalParser
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\MobileNaturalParser
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\AnswerBox
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalResult
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalWithLargeVideo
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResult
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsVideoResult
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResultZ1m
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResultZINbbc
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Divider
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Flight
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ImageGroup
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ImageGroupCarousel
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\InTheNews
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\KnowledgeCard
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Map
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\PeopleAlsoAsk
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SearchResultGroup
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TopStoriesVertical
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TopStoriesCarousel
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ComposedTopStories
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TweetsCarousel
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TweetsCarouselZ1m
 * @covers \Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\VideoGroup
 *
 */
class NaturalParserTest extends GoogleSerpTestCase
{

    public function serpProvider()
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(__DIR__ . '/natural-parser-data')
        );
        $data = [];
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'yml') {
                $data[] = [$file->getRealPath()];
            }
        }
        return $data;
    }


    /**
     * @dataProvider serpProvider
     */
    public function testSerps($file)
    {
        $data = Yaml::parse(file_get_contents($file));

        $gUrl = GoogleUrlArchive::fromString($data['url']);
        $dom = new GoogleSerp(file_get_contents($data['file']), $gUrl);

        $result = $dom->getNaturalResults();

        if (isset($data['test-methods'])) {
            foreach ($data['test-methods'] as $method => $methodData) {
                $this->assertEquals($methodData, call_user_func([$dom, $method]), "Method $method failed.");
            }
        }

        if (isset($data['results'])) {
            $this->assertCount(count($data['results']), $result->getItems(), 'Failed asserting that number of results matched. Using file ' . $file);

            foreach ($data['results'] as $k => $expectedResult) {
                $item = $result->getItems()[$k];
                $this->assertResultHasTypes($expectedResult['types'], $item, $file, $k);

                if (isset($expectedResult['not-types'])) {
                    $this->assertResultDoesNotHaveTypes($expectedResult['not-types'], $item);
                }

                if (isset($expectedResult['data'])) {
                    $this->assertResultHasData($expectedResult['data'], $item, $file . ' => ' . $k . '/');
                }
                if (isset($expectedResult['data-count'])) {
                    $this->assertResultDataCount($expectedResult['data-count'], $item);
                }
                if (isset($expectedResult['data-media'])) {
                    $this->assertResultHasDataMedia($expectedResult['data-media'], $item);
                }
            }
        }

        if (isset($data['related-searches'])) {
            $relatedSearches = $dom->getRelatedSearches();

            $this->assertCount(count($data['related-searches']), $relatedSearches, 'Failed related search assertion with file ' . $file);

            foreach ($data['related-searches'] as $k => $relSearchItem) {
                if (isset($relSearchItem['title'])) {
                    $this->assertEquals($relSearchItem['title'], $relatedSearches[$k]->title, 'Failed related search title assertion with file ' . $file . ' for item #' . $k);
                }
                if (isset($relSearchItem['url'])) {
                    $this->assertEquals($relSearchItem['url'], $relatedSearches[$k]->url, 'Failed related search url assertion with file ' . $file . ' for item #' . $k);
                }
            }
        }

        if (isset($data['ads'])) {
            $allAdsResults = $dom->getAdwordsResults();

            if (isset($data['ads']['section'])) {
                foreach ($data['ads']['section'] as $section => $sectionData) {
                    if (isset($sectionData['results'])) {
                        $sectionConstant = 'SECTION_' . strtoupper($section);


                        $adResults = $allAdsResults->getResultsByType('adws_section_' . $section);

                        $this->assertCount(
                            count($sectionData['results']),
                            $adResults,
                            'Failed asserting that number of adwords results matched. Using file ' . $file . ' | ' . $section
                        );

                        foreach ($sectionData['results'] as $k => $expectedResult) {
                            $item = $adResults[$k];
                            $this->assertResultHasTypes(
                                array_merge($expectedResult['types'], [$sectionConstant]),
                                $item,
                                $file,
                                $k,
                                AdwordsResultType::class
                            );

                            if (isset($expectedResult['data'])) {
                                $this->assertResultHasData($expectedResult['data'], $item, $file . ' => ' . $k . '/');
                            }
                        }
                    }
                }
            }
        }
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
            'https://www.google.fr/search?client=ubuntu&hs=k8t&q=shop+near+paris&npsic=0&rflfq=1&rlha=0&rllag=48865798%2C2325372%2C1412&tbm=lcl&ved=0ahUKEwj9h5Ha7aTOAhWEAxoKHRlCAI4QtgMIJw&tbs=lf%3A1%2Clf_ui%3A2',
            (string)$map->getDataValue('mapUrl')
        );
        $this->assertCount(3, $map->localPack);
        $this->assertEquals('Disney Store', $map->localPack[0]->title);
        $this->assertequals('http://www.disneystore.fr/', $map->localPack[0]->url);
        $this->assertEquals('44 Av. des Champs-Élysées', $map->localPack[0]->street);
        // Stars
        $this->assertEquals('4.1', $map->localPack[0]->stars);
        $this->assertEquals('4.0', $map->localPack[1]->stars);
        // Review
        $this->assertEquals(null, $map->localPack[0]->review);
        $this->assertEquals(null, $map->localPack[1]->review);
        $this->assertEquals(null, $map->localPack[2]->review);
        // Phone
        $this->assertEquals('01 45 61 45 25', $map->localPack[0]->phone);
        $this->assertEquals('01 44 94 09 40', $map->localPack[1]->phone);
        $this->assertEquals('01 40 13 99 93', $map->localPack[2]->phone);
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

    public function testResultWithDomText()
    {
        $gUrl = GoogleUrlArchive::fromString('https://www.google.co.uk/search?q=foo');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/with-DOMText.html'), $gUrl);

        $naturalParser = new NaturalParser();
        $results = $naturalParser->parse($dom);
    }
}

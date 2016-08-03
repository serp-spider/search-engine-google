<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\AdwordsResultType;
use Serps\SearchEngine\Google\Parser\Evaluated\AdwordsParser;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\GoogleUrlArchive;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\Core\Serp\CompositeResultSet;

/**
 * Testing parser is hard, because it relies on google pages
 *
 * The tests here are parsing a saved html version of a google page.
 * They do not prevent google from changing its dom. If it
 * happens the saved html and the following tests must be updated.
 *
 * When the tests are updated, make sure that the new one include the same kind of results.
 * For instance if the previous test included a ``Shopping`` item, then the new test should do so.
 *
 *
 * @covers Serps\SearchEngine\Google\Parser\AbstractParser
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\AdwordsParser
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\AdwordsSectionParser
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Adwords\AdwordsItem
 * @covers Serps\SearchEngine\Google\Parser\Evaluated\Rule\Adwords\Shopping
 * @covers Serps\SearchEngine\Google\Css
 */
class AdwordsParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParserTopAndBottom()
    {

        $gUrl = GoogleUrlArchive::fromString('https://www.google.com.au/search?q=simpsons+poster&hl=en_US');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-evaluated/adwords/simpsons+poster.html'), $gUrl);

        $parser = new  AdwordsParser();
        $results = $parser->parse($dom);

        $this->assertInstanceOf(CompositeResultSet::class, $results);

        $this->assertCount(5, $results);

        $this->assertCount(2, $results->getResultsByType(AdwordsResultType::SECTION_BOTTOM));
        $this->assertCount(3, $results->getResultsByType(AdwordsResultType::SECTION_TOP));


        // TESTING TOP
        $topItemp = $results->getItems()[1];
        $this->assertEquals(
            'Art Posters On Sale Today - allposters.com.au‎',
            $topItemp->getDataValue('title')
        );

        $this->assertEquals(
            'http://www.allposters.com.au/?AID=1195529028&KWID=2003592128',
            $topItemp->getDataValue('url')
        );

        $this->assertEquals(
            'www.allposters.com.au/OfficialSite',
            $topItemp->getDataValue('visurl')
        );

        $this->assertEquals(
            'Save 30% Or More When You Buy Now. Plus Easy Returns & Fast Shipping!',
            $topItemp->getDataValue('description')
        );

        // TESTING BOTTOM
        $bottomItem = $results->getItems()[3];
        $this->assertEquals(
            'Votre Simpsons Poster‎',
            $bottomItem->getDataValue('title')
        );

        $this->assertEquals(
            'http://www.allposters.fr/-st/Les-Simpsons-Affiches_c7902_.htm?AID=1410937278&KWID=705639909',
            $bottomItem->getDataValue('url')
        );

        $this->assertEquals(
            'www.allposters.fr/',
            $bottomItem->getDataValue('visurl')
        );

        $this->assertEquals(
            'Vos Posters de Séries TV à Prix Bas 500.000 Posters, Cadres Disponibles',
            $bottomItem->getDataValue('description')
        );

        // Testing Shopping
        $shoppingItem = $results->getItems()[0];
        $this->assertTrue($shoppingItem->is(AdwordsResultType::SHOPPING_GROUP));
        $this->assertCount(5, $shoppingItem->getDataValue('products'));
        $this->assertEquals('Affiche Simpsons-Cast', $shoppingItem->getDataValue('products')[0]->title);
        $this->assertStringStartsWith('data:image/jpeg;base64,/9j/4A', $shoppingItem->getDataValue('products')[0]->image);
        $this->assertStringEndsWith('K1lrSpf/2Q==', $shoppingItem->getDataValue('products')[0]->image);
        $this->assertEquals('https://www.google.com.au/aclk?sa=l&ai=CY0A_jlP5Vuu_FpDmzAal85fABeihh8sF4LeUmKwBl9bV_YcDCAQQASgFYPsBoAH7_Zf-A8gBB6oEJ0_QX-g3vvmVLal5IsrVSmuL8KuTKi8rF8WopQL8xEMXFSCmtG7iHMAFBaAGJoAH2KX4H5AHA6gHpr4b2AcB4BLxv6bTjuzD6I4B&sig=AOD64_1iHykYRDusLJqdU94-aFjnHM1TuA&ctype=5&clui=11&q=&ved=0ahUKEwi-g9rZ3uPLAhUG1RoKHWKYArQQww8IHw&adurl=http://www.allposters.fr/-sp/Simpsons-Cast-Names_i8574538_.htm%3FAID%3D815014090%26ProductTarget%3D105221810967', $shoppingItem->getDataValue('products')[0]->url);
        $this->assertEquals('AllPosters.fr', $shoppingItem->getDataValue('products')[0]->target);
        $this->assertEquals('EUR9.99', $shoppingItem->getDataValue('products')[0]->price);
    }
}

<?php
/**
 * @license see LICENSE
 */

namespace Serps\Test\TDD\SearchEngine\Google\Parser\Raw;

use Serps\SearchEngine\Google\AdwordsResultType;
use Serps\SearchEngine\Google\Parser\Raw\AdwordsParser;
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
 * @covers Serps\SearchEngine\Google\Parser\Raw\AdwordsParser
 * @covers Serps\SearchEngine\Google\Parser\Raw\AdwordsSectionParser
 * @covers Serps\SearchEngine\Google\Parser\Raw\Rule\Adwords\AdwordsItem
 * @covers Serps\SearchEngine\Google\Parser\Raw\Rule\Adwords\Shopping
 * @covers Serps\SearchEngine\Google\Css
 */
class AdwordsParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParserTopAndRight()
    {

        $gUrl = GoogleUrlArchive::fromString('https://www.google.co.uk/search?q=simpsons+poster');
        $dom = new GoogleDom(file_get_contents('test/resources/pages-raw/adwords/simpsons+poster.html'), $gUrl);

        $parser = new  AdwordsParser();
        $results = $parser->parse($dom);



        $this->assertInstanceOf(CompositeResultSet::class, $results);

        $this->assertCount(4, $results);

        $this->assertCount(3, $results->getResultsByType(AdwordsResultType::SECTION_TOP));
        $this->assertCount(1, $results->getResultsByType(AdwordsResultType::SECTION_RIGHT));

        $this->assertCount(3, $results->getResultsByType(AdwordsResultType::AD));
        $this->assertCount(1, $results->getResultsByType(AdwordsResultType::SHOPPING_GROUP));
 


        // TESTING TOP
        $topItems = $results->getItems()[1];
        $this->assertEquals(
            'Simpsons Poster Sale - AllPosters.co.uk',
            utf8_decode($topItems->getDataValue('title'))
        );


        $this->assertEquals(
            'http://www.AllPosters.co.uk/-st/Simpsons-Posters_c7902_.htm?AID=252016255&KWID=47931260&VTP=Start&NetWorkType=g&PAdCopyId=1425841444&ClickPos=1t2&GeolociId=9055538&IntLocId=2826&AudId=kwd-96365764&Device=c&VTP=End',
            $topItems->getDataValue('url')
        );

        $this->assertEquals(
            'www.allposters.co.uk/',
            $topItems->getDataValue('visurl')
        );

        $this->assertEquals(
            'Find any Simpsons Poster at The World\'s Largest Poster Store!',
            $topItems->getDataValue('description')
        );





        // TESTING RIGHT SHOPPING

        $shoppingItem = $results->getItems()[3];

        $this->assertTrue($shoppingItem->is(AdwordsResultType::SHOPPING_GROUP));
        $this->assertCount(4, $shoppingItem->getDataValue('products'));
        $this->assertEquals('Affiche Simpsons- ...', $shoppingItem->getDataValue('products')[0]->getDataValue('title'));
        $this->assertEquals(
            'https://encrypted-tbn1.gstatic.com/shopping?q=tbn:ANd9GcRfqtzy1dIsPU0bITtAoNXs97_pOAaJHeGIWCIGZWn06INt7mCxMJc7H8aY0qhRwQScH4Dm&usqp=CAc',
            $shoppingItem->getDataValue('products')[0]->getDataValue('image')
        );
        $this->assertEquals('http://www.google.co.uk/aclk?sa=l&ai=CawT9OrMUV9naMaKdywPcnLlI6KGHywXgt5SYrAGX1tX9hwMIBhABKARg-wGgAfv9l_4DyAEHqgQnT9B1IP6jfLu6QBobxCOfm7V4WhGKn4FhBypaeSSqWkv2GHrxY6dlwAUFoAYmgAfYpfgfkAcDqAemvhvYBwHgEvG_ptOO7MPojgE&sig=AOD64_2iNaDbYjPJE7CvLDxjaiK79qA10g&ctype=5&clui=15&ved=0ahUKEwiuhM7e-ZfMAhVLEpoKHU-LDNYQ9A4IbQ&adurl=http://www.allposters.fr/-sp/Simpsons-Cast-Names_i8574538_.htm%3FAID%3D815014090%26ProductTarget%3D105221810967', $shoppingItem->products[0]->url);
        $this->assertEquals('AllPosters.fr', $shoppingItem->getDataValue('products')[0]->target);
        $this->assertEquals('€9.99', $shoppingItem->getDataValue('products')[0]->price);


    }
}

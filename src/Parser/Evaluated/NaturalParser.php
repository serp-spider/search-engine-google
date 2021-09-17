<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\AbstractParser;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\AdsTop;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\AnswerBox;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Divider;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Flight;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ImageGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\InTheNews;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\KnowledgeGraph;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Map;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\MapLegacy;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\PeopleAlsoAsk;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Recipes;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SearchResultGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TopStoriesCarousel;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TopStoriesVertical;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TweetsCarousel;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalWithLargeVideo;

/**
 * Parses natural results from a google SERP
 */
class NaturalParser extends AbstractParser
{

    /**
     * @inheritdoc
     */
    protected function generateRules()
    {
        return [
            new Divider(),
            new SearchResultGroup(),
            //new ClassicalResult(),
            //new ClassicalCardsResult(),
            new ImageGroup(),
            new TopStoriesCarousel(),
            new TopStoriesVertical(),
            new TweetsCarousel(),
            //new ClassicalWithLargeVideo(),
            new InTheNews(),
            new Map(),
            new AnswerBox(),
            new Flight(),
            new PeopleAlsoAsk(),
            new KnowledgeGraph(),
            new AdsTop(),
            new Recipes(),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getParsableItems(GoogleDom $googleDom)
    {
        // rso = results in position
        // rhs = knowledge graph
        // iur = images
        // tvcap = ads top
        // tvcap = ads top
        // @jsname='gI9xcc' = recipes
       // return $googleDom->xpathQuery("//*[@id = 'rso' or @id='rhs' or @id='taw']/*[not(self::script) and not(self::style)]/*");
        return $googleDom->xpathQuery("//*[@jsname='gI9xcc']/*[not(self::script) and not(self::style)]/*");
    }
}

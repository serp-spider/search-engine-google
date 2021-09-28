<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\AbstractParser;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\AdsTop;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\AnswerBox;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\AppPack;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Divider;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\FeaturedSnipped;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Flight;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ImageGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\InTheNews;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Jobs;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\KnowledgeGraph;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Maps;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\MapLegacy;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\PeopleAlsoAsk;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ProductListing;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Questions;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Recipes;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Hotels;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Definitions;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Flights;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SearchResultGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TopStories;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TopStoriesCarousel;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TopStoriesVertical;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TweetsCarousel;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalWithLargeVideo;

/**
 * Parses natural results from a google SERP
 */
class NaturalParser extends AbstractParser
{
    protected $isMobile = true;

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
            new Maps(),
            new AnswerBox(),
            new Flight(),
            new PeopleAlsoAsk(),
            new KnowledgeGraph(),
            new AdsTop(),
            new Recipes(),
            new TopStories(),
            new FeaturedSnipped(),
            new ProductListing(),
            new Questions(),
            new Hotels(),
            new Definitions(),
            new Flights(),
            new Jobs(),
            new AppPack(),

        ];
    }

    /**
     * @inheritdoc
     */
    protected function getParsableItems(GoogleDom $googleDom)
    {
        // [@id='rso'] = results in position
        // [@id='rhs']  = knowledge graph
        // [@id='iur'] = images
        // [@id='tads']  = ads top
        // [@id='tadsb']  = ads bottom
        // [@id='tvcap']  = ads top carousel
        // [@id='extabar']  =app pack
        // [@class='C7r6Ue']  = maps
        // [@class='xpdopen']  = features snipped/position zero
        // [contains(@class, 'commercial-unit-desktop-top')]  = product listing
        // [contains(@class, 'related-question-pair')]  = questions
        // [contains(@class, 'gws-plugins-horizon-jobs__li-ed')]  = jobs

        //  div[contains(@id, 'isl')]  = recipes
        // //*[g-section-with-header[@class='yG4QQe TBC9ub']]]/child::* = top stories
        //div[@class='CH6Bmd']/div[@class='ntKMYc P2hV9e'] = hotels
        //@class='lr_container yc7KLc mBNN3d' - definitions
        //@class='LQQ1Bd' - flights
       // return $googleDom->xpathQuery("//*[@id = 'rso' or @id='rhs' or @id='taw']/*[not(self::script) and not(self::style)]/*");
        return $googleDom->xpathQuery("//div[@class='lr_container yc7KLc mBNN3d'][not(self::script) and not(self::style)]");
    }
}


<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\AbstractParser;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\AdsTop;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\AdsTopMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\AppPackMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResultO9g5cc;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardResultsATSHe;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResultZ1m;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResultZINbbc;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsVideoResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ComposedTopStories;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Definitions;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\DefinitionsMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Divider;

use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\FeaturedSnipped;

use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Flights;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Hotels;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\HotelsMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ImageGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ImageGroupCarousel;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\LargeClassicalResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\KnowledgeCard;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\KnowledgeGraphMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\MapMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Maps;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\MapsMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\PeopleAlsoAsk;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ProductListing;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ProductListingMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Questions;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Recipes;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SearchResultGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TopStories;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TopStoriesMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TweetsCarouselZ1m;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\VideoGroup;

/**
 * Parses natural results from a mobile google SERP
 */
class MobileNaturalParser extends AbstractParser
{

    protected $isMobile = true;
    /**
     * @inheritdoc
     */
    protected function generateRules()
    {
        return [
            new Divider(),
            new ImageGroup(),
            new MapsMobile(),
            new Questions(),
            new TopStoriesMobile(),
            new ProductListingMobile(),
            new KnowledgeGraphMobile(),
            new AdsTopMobile(),
            new AppPackMobile(),
            new FeaturedSnipped(),
            new Recipes(),
            new Flights(),
            new HotelsMobile(),
            new DefinitionsMobile()
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getParsableItems(GoogleDom $googleDom)
    {
        // [@id='iur'] = images
        // [contains(@class, 'scm-c')]  = maps
        // [contains(@class, 'related-question-pair')] = questions
        // [@class='C7r6Ue']  = maps
        // [@class='xSoq1']  = top stories

        // [contains(@class, 'commercial-unit-mobile-top')]  = product listing
        // [contains(@class, 'osrp-blk')]  =  knowledge graph
        // [@id='tads']  = ads top
        // [@id='tadsb']  = ads bottom
        // [[contains(@class, 'qs-io')]]  =app pack
        // [@class='xpdopen']  = features snipped/position zero
        //[contains(@class, 'gws-plugins-horizon-jobs__li-ed')]  = jobs
        //@class='LQQ1Bd' - flights
        //div[@class='hNKF2b m9orme'] = hotels
        //div[@class='lr_container wDYxhc yc7KLc'] = definitions
        //div[contains(@id, 'isl')]  = recipes

        return $googleDom->xpathQuery("//*[
            @id='iur' or
            @id='rso' or
            @id='tads' or
            @id='tadsb'
            contains(@class, 'scm-c') or
            contains(@class, 'related-question-pair') or
            @class='C7r6Ue' or
            contains(@class, 'commercial-unit-mobile-top') or
            contains(@class, 'osrp-blk') or
            contains(@class, 'qs-io') or
            @class='xpdopen' or
            contains(@class, 'gws-plugins-horizon-jobs__li-ed') or
            @class='LQQ1Bd' or
            @class='hNKF2b m9orme' or
            div[@class='lr_container wDYxhc yc7KLc'] or
            div[contains(@id, 'isl')]
        ][not(self::script) and not(self::style)]");
    }
}

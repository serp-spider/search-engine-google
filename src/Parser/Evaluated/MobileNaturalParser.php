<?php
namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\AbstractParser;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\AdsTopMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\AppPackMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalResultMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\LargeClassicalResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\DefinitionsMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\DirectionsMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\FeaturedSnipped;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Flights;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\HotelsMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ImageGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\KnowledgeGraphMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\MapsMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\MisspellingMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\PeopleAlsoAsk;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ProductListing;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ProductListingMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Questions;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Recipes;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TopStoriesMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\VideoGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\VideosMobile;

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
            new ClassicalResultMobile(),
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
            new DefinitionsMobile(),
            new VideosMobile(),
            new MisspellingMobile(),
            new DirectionsMobile()
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
        //  @class='cawG4b OvQkSb' = videos
        //  @class='uVMCKf mnr-c' = videos
        // [contains(@class, 'commercial-unit-mobile-top')]  = product listing
        // [contains(@class, 'commercial-unit-mobile-bottom')]  = product listing
        // [contains(@class, 'osrp-blk')]  =  knowledge graph
        // [@id='tads']  = ads top
        // [@id='tadsb']  = ads bottom
        // [[contains(@class, 'qs-io')]]  =app pack
        // [[contains(@class, 'ki5rnd')]]  =app pack
        // [@class='xpdopen']  = features snipped/position zero
        //[contains(@class, 'gws-plugins-horizon-jobs__li-ed')]  = jobs
        //@class='LQQ1Bd' - flights
        //div[@class='hNKF2b m9orme'] = hotels
        //div[@class='lr_container wDYxhc yc7KLc'] = definitions
        //div[contains(@id, 'isl')]  = recipes
        //@class='p64x9c card-section KDCVqf mnr-c' - misspelings
        //@class='ULktNd rQUFld mnr-c rrecc' - directions

        return $googleDom->xpathQuery("//*[@id='iur' or @id='rso' or
            @id='tads' or
            @id='tadsb' or
            contains(@class, 'scm-c') or
            contains(@class, 'related-question-pair') or
            @class='C7r6Ue' or
            contains(@class, 'commercial-unit-mobile-top') or
            contains(@class, 'commercial-unit-mobile-bottom') or
            contains(@class, 'osrp-blk') or
            contains(@class, 'qs-io') or
            contains(@class, 'ki5rnd') or
            @class='xpdopen' or
            contains(@class, 'gws-plugins-horizon-jobs__li-ed') or
            @class='LQQ1Bd' or
            @class='xSoq1' or
            @class='cawG4b OvQkSb' or
            @class='uVMCKf mnr-c' or
            @class='hNKF2b m9orme' or
            div[@class='lr_container wDYxhc yc7KLc'] or
            div[contains(@id, 'isl')] or
            contains(@class, 'kp-wholepage') or
            @class = 'p64x9c card-section KDCVqf mnr-c' or
            @class='ULktNd rQUFld mnr-c rrecc'
        ][not(self::script) and not(self::style)]");
    }
}

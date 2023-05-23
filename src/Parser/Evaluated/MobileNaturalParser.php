<?php
namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Monolog\Logger;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\AbstractParser;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\AdsTopMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\AppPackMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalResultMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalResultMobileV2;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\DefinitionsMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\DirectionsMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\FeaturedSnipped;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Flights;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\HotelsMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ImageGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Jobs;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\KnowledgeGraphMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\MapsMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\MisspellingMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\NoMoreResults;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\PeopleAlsoAsk;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ProductListing;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ProductListingMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Questions;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Recipes;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TopStoriesMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\VideoCarouselMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\VideosMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\VisualDigest;

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
            new ClassicalResultMobile($this->logger),
            new ClassicalResultMobileV2($this->logger),
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
            new Jobs(),
            new HotelsMobile(),
            new DefinitionsMobile(),
            new VideosMobile(),
            new MisspellingMobile(),
            new DirectionsMobile(),
            new VideoCarouselMobile(),
            new NoMoreResults(),
            new VisualDigest()
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getParsableItems(GoogleDom $googleDom)
    {
        // [@id='iur'] = images
        // [@id='sports-app'] = classical results
        // [contains(@class, 'scm-c')]  = maps
        // [contains(@class, 'qixVud')]  = maps
        // [contains(@class, 'xxAJT')]  = maps
        // [contains(@class, 'related-question-pair')] = questions
        // [@class='C7r6Ue']  = maps
        // [@class='qixVud']  = maps
        // [@class='xSoq1']  = top stories
        //  @class='cawG4b OvQkSb' = videos
        //  @class='uVMCKf mnr-c' = videos
        //  @class='HD8Pae mnr-c' = videos
        // [contains(@class, 'commercial-unit-mobile-top')]  = product listing
        // [contains(@class, 'commercial-unit-mobile-bottom')]  = product listing
        // [contains(@class, 'osrp-blk')]  =  knowledge graph
        // [@id='tads']  = ads top
        // [@id='tadsb']  = ads bottom
        // [@id='bottomads']  = ads bottom
        // [[contains(@class, 'qs-io')]]  =app pack
        // [[contains(@class, 'ki5rnd')]]  =app pack
        // [@class='xpdopen']  = features snipped/position zero
        //[contains(@class, 'gws-plugins-horizon-jobs__li-ed')]  = jobs
        //[contains(@class, 'LQQ1Bd')] - flights
        //div[@class='hNKF2b'] = hotels
        //div[@class='lr_container wDYxhc yc7KLc'] = definitions
        // @jsname='MGJTwe'  = recipes
        //@id='oFNiHe' - misspelings
        //@id='lud-ed' directions
        //contains(@class, 'e8Ck0d') visual digest
        return $googleDom->xpathQuery("//*[@id='iur' or
            @id='sports-app' or
            @id='center_col' or
            @id='tads' or
            @id='tadsb' or
            @id='bottomads' or
            contains(@class, 'scm-c') or
            contains(@class, 'related-question-pair') or
            @class='C7r6Ue' or
            contains(@class, 'qixVud') or
            contains(@class, 'xxAJT') or
            contains(@class, 'commercial-unit-mobile-top') or
            contains(@class, 'commercial-unit-mobile-bottom') or
            contains(@class, 'osrp-blk') or
            contains(@class, 'qs-io') or
            contains(@class, 'ki5rnd') or
            @class='xpdopen' or
            contains(@class, 'gws-plugins-horizon-jobs__li-ed') or
            contains(@class, 'LQQ1Bd') or
            @class='xSoq1' or
            @class='cawG4b OvQkSb' or
            @class='uVMCKf mnr-c' or
            contains(@class, 'HD8Pae mnr-c') or
            contains(@class, 'hNKF2b') or
            contains(@class, 'lr_container wDYxhc yc7KLc') or
            @jsname='MGJTwe'  or
            contains(@class, 'kp-wholepage') or
            @id = 'oFNiHe' or
            @id='lud-ed' or
            video-voyager or
            inline-video or
            @id= 'ofr' or
            contains(@class, 'e8Ck0d')
        ][not(self::script) and not(self::style)]");
    }
}

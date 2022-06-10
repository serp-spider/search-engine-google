<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\AbstractParser;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\AdsTop;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Definitions;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Directions;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\FeaturedSnipped;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Flight;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Flights;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Hotels;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ImageGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Jobs;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\KnowledgeGraph;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Maps;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\MapsCoords;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Misspelling;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ProductListing;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Questions;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Recipes;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ResultsNo;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TopStories;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Videos;

/**
 * Parses natural results from a google SERP
 */
class NaturalParser extends AbstractParser
{
    protected $isMobile = false;

    /**
     * @inheritdoc
     */
    protected function generateRules()
    {
        return [
            new ClassicalResult($this->logger),
            new ImageGroup(),
            new Videos(),
            new Maps(),
            new Flight(),
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
            new ResultsNo(),
            new Directions(),
            new MapsCoords(),
            new Misspelling()
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
        // [@class='e4xoPb']  = videos
        // [contains(@class, 'commercial-unit-desktop-top')]  = product listing
        // [contains(@class, 'cu-container')]  = product listing on right, like ads
        // [contains(@class, 'related-question-pair')]  = questions
        // [contains(@class, 'gws-plugins-horizon-jobs__li-ed')]  = jobs
        // [@class='xpdopen']  = features snipped/position zero
        //  div[contains(@id, 'isl')]  = recipes
        // //*[g-section-with-header[@class='yG4QQe TBC9ub']]]/child::* = top stories
        // [@id='kp-wp-tab-cont-Latest'] = top stories
        //div[@class='CH6Bmd']/div[@class='ntKMYc P2hV9e'] = hotels
        //@class='lr_container yc7KLc mBNN3d' - definitions
        //@class='LQQ1Bd' - flights
        //@id = 'oFNiHe' - misspelings
        //@id = 'result-stats' - no of results
        //@class = 'ULktNd rQUFld rrecc' - directions
//        return $googleDom->xpathQuery("//*[@id='result-stats']/*[not(self::script) and not(self::style)]/*");
//        @class = 'H93uF' - coords
        return $googleDom->xpathQuery("//*[
            @id='rso' or
            @id='rhs' or
            @id='iur' or
            @id='tads' or
            @id='tadsb' or
            @id='tvcap' or
            @id='extabar' or
            contains(@id, 'isl') or
            @class='C7r6Ue' or
            @class='e4xoPb' or
            @class='WVGKWb' or
            @class='xpdopen' or
            @class='lr_container yc7KLc mBNN3d' or
            @class='LQQ1Bd' or
            div[@class='CH6Bmd'] or
            contains(@class, 'commercial-unit-desktop-top') or
            contains(@class, 'cu-container') or
            contains(@class, 'related-question-pair') or
            contains(@class, 'gws-plugins-horizon-jobs__li-ed') or
            g-section-with-header[@class='yG4QQe TBC9ub'] or
            @id='kp-wp-tab-cont-Latest' or
            @id = 'oFNiHe' or
            @id='result-stats' or
            @id='kp-wp-tab-Latest' or
            @class = 'ULktNd rQUFld rrecc' or
            @class = 'H93uF' or
            @class = 'o8ebK'
        ][not(self::script) and not(self::style)]");
    }
}


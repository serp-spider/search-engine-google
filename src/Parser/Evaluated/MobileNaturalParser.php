<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\AbstractParser;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResultO9g5cc;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardResultsATSHe;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResultZ1m;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResultZINbbc;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsVideoResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ComposedTopStories;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Definitions;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\DefinitionsMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Divider;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Flights;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Hotels;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\HotelsMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ImageGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ImageGroupCarousel;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\LargeClassicalResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\KnowledgeCard;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\MapMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Maps;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\MapsMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\PeopleAlsoAsk;
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
        //@class='LQQ1Bd' - flights
        //div[@class='hNKF2b m9orme'] = hotels
        //div[@class='lr_container wDYxhc yc7KLc'] = definitions

        return $googleDom->xpathQuery("//*[@class='lr_container wDYxhc yc7KLc'][not(self::script) and not(self::style)]");
    }
}

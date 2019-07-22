<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\AbstractParser;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResultO9g5cc;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResultZ1m;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResultZINbbc;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsVideoResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ComposedTopStories;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Divider;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ImageGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ImageGroupCarousel;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\LargeClassicalResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\KnowledgeCard;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalCardsResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\MapMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\PeopleAlsoAsk;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SearchResultGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TweetsCarouselZ1m;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\VideoGroup;

/**
 * Parses natural results from a mobile google SERP
 */
class MobileNaturalParser extends AbstractParser
{

    /**
     * @inheritdoc
     */
    protected function generateRules()
    {
        return [
            new Divider(),
            new SearchResultGroup(),
            new ClassicalCardsResultO9g5cc(),
            new ClassicalCardsResultZINbbc(), // TODO maybe outdated
            new ClassicalCardsResultZ1m(), // TODO remove (outdated)
            new ClassicalCardsVideoResult(),
            new ClassicalCardsResult(),
            new MapMobile(),
            new TweetsCarouselZ1m(), // TODO replace and remove (outdated)
            new ImageGroupCarousel(),
            new ComposedTopStories(),
            new VideoGroup(),
            new ImageGroup(),
            new PeopleAlsoAsk(), // people also ask must be placed before knowledge card to stop parsing
            new KnowledgeCard()
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getParsableItems(GoogleDom $googleDom)
    {
        $xpathObject = $googleDom->getXpath();
        $xpathElementGroups = "//div[@id = 'ires']/*[@id = 'rso']/*";
        return $xpathObject->query($xpathElementGroups);
    }
}

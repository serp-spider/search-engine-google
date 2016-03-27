<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\AbstractParser;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\ClassicalResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Divider;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\ImageGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\InDepthArticle;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\InTheNews;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Map;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\SearchResultGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\TweetsCarousel;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\ClassicalWithLargeVideo;

/**
 * Parses adwords results from a google SERP
 */
class AdwordsParser extends AbstractParser
{

    /**
     * @inheritdoc
     */
    protected function generateRules()
    {
        return [
            new Divider(),
            new SearchResultGroup(),
            new ClassicalResult(),
            new ImageGroup(),
            new TweetsCarousel(),
            new ClassicalWithLargeVideo(),
            new InTheNews(),
            new Map(),
            new InDepthArticle()
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

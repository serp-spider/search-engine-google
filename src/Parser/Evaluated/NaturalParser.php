<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\AbstractParser;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ClassicalResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Divider;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ImageGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\InTheNews;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\LargeClassicalResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Map;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SearchResultGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\TweetsCarousel;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\ClassicalWithLargeVideo;

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
            new ClassicalResult(),
            new LargeClassicalResult(),
            new ImageGroup(),
            new TweetsCarousel(),
            new ClassicalWithLargeVideo(),
            new InTheNews(),
            new Map()
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

<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\AbstractNaturalParser;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\ClassicalResult;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Divider;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\ImageGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\InTheNews;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\SearchResultGroup;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\TweetsCarousel;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Video;

/**
 * Parses natural results from a google SERP
 */
class NaturalParser extends AbstractNaturalParser
{

    /**
     * @inheritdoc
     */
    protected function generateRules()
    {
        return [
            new Divider(),
            new ClassicalResult(),
            new SearchResultGroup(),
            new TweetsCarousel(),
            new ImageGroup(),
            new Video(),
            new InTheNews()
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

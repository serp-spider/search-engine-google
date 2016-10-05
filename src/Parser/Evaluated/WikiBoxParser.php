<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\Parser\Evaluated\Rule\WikiBox\Header;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\WikiBox\Description;
use Serps\SearchEngine\Google\Css;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\AbstractParser;

/**
 * Parses natural results from a google SERP
 */
class WikiBoxParser extends AbstractParser
{
    /**
     * @inheritdoc
     */
    protected function generateRules()
    {
        return [
            new Header(),
            new Description(),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getParsableItems(GoogleDom $googleDom)
    {
        $xpathObject = $googleDom->getXpath();
        $xpath = Css::toXPath('div#rhs div._OKe > div > div');

        return $xpathObject->query($xpath);
    }
}

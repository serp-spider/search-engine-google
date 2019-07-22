<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\AdwordsResultType;
use Serps\Core\Dom\Css;
use Serps\SearchEngine\Google\Parser\AbstractAdwordsParser;

class AdwordsParser extends AbstractAdwordsParser
{

    /**
     * @inheritdoc
     */
    public function generateParsers()
    {
        return [
            // Adwords top
            new AdwordsSectionParser(
                Css::toXPath('div#tads li.ads-ad, div#tvcap ._oc'),
                AdwordsResultType::SECTION_TOP
            ),

            // Adwords bottom
            new AdwordsSectionParser(
                "descendant::div[@id = 'bottomads']//li[@class='ads-ad']",
                AdwordsResultType::SECTION_BOTTOM
            )
        ];
    }
}

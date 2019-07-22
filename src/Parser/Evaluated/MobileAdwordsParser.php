<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\AdwordsResultType;
use Serps\Core\Dom\Css;
use Serps\SearchEngine\Google\Parser\AbstractAdwordsParser;

class MobileAdwordsParser extends AbstractAdwordsParser
{

    /**
     * @inheritdoc
     */
    public function generateParsers()
    {
        return [
            // Adwords top
            new MobileAdwordsSectionParser(
                Css::toXPath('#tads li.ads-fr, #tvcap'),
                AdwordsResultType::SECTION_TOP
            ),

            // Adwords bottom
            new MobileAdwordsSectionParser(
                Css::toXPath('#tadsb li.ads-fr'),
                AdwordsResultType::SECTION_BOTTOM
            )
        ];
    }
}

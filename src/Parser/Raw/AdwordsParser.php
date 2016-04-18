<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Raw;

use Serps\Core\Serp\CompositeResultSet;
use Serps\SearchEngine\Google\AdwordsResultType;
use Serps\SearchEngine\Google\Css;
use Serps\SearchEngine\Google\Page\GoogleDom;
use \Serps\SearchEngine\Google\Parser\Raw\AdwordsSectionParser;

class AdwordsParser
{

    /**
     * @param GoogleDom $googleDom
     * @return CompositeResultSet
     */
    public function parse(GoogleDom $googleDom)
    {
        $parsers = [
            // Adwords top
            new AdwordsSectionParser(
                Css::toXPath('div#_Ltg li.ads-ad'),
                AdwordsResultType::SECTION_TOP
            ),

            // Adwords right
            new AdwordsSectionParser(
                Css::toXPath('#rhs_block ._oc'),
                AdwordsResultType::SECTION_RIGHT
            )
        ];


        $resultsSets = new CompositeResultSet();

        foreach ($parsers as $parser) {
            /* @var $parser \Serps\SearchEngine\Google\Parser\Raw\AdwordsSectionParser */
            $resultsSets->addResultSet(
                $parser->parse($googleDom)
            );
        }

        return $resultsSets;
    }
}

<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Serps\Core\Serp\CompositeResultSet;
use Serps\SearchEngine\Google\AdwordsResultType;
use Serps\Core\Dom\Css;
use Serps\SearchEngine\Google\Page\GoogleDom;
use \Serps\SearchEngine\Google\Parser\Evaluated\AdwordsSectionParser;

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
                Css::toXPath('div#tads li.ads-ad, div#tvcap ._oc'),
                AdwordsResultType::SECTION_TOP
            ),

            // Adwords bottom
            new AdwordsSectionParser(
                "descendant::div[@id = 'bottomads']//li[@class='ads-ad']",
                AdwordsResultType::SECTION_BOTTOM
            )
        ];


        $resultsSets = new CompositeResultSet();

        foreach ($parsers as $parser) {
            /* @var $parser \Serps\SearchEngine\Google\Parser\Evaluated\AdwordsSectionParser */
            $resultsSets->addResultSet(
                $parser->parse($googleDom)
            );
        }

        return $resultsSets;
    }
}

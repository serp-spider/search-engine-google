<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Serps\Core\Serp\CompositeResultSet;
use Serps\SearchEngine\Google\AdwordsResultType;
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
            new AdwordsSectionParser(AdwordsSectionParser::ADS_SECTION_TOP_XPATH, AdwordsResultType::SECTION_TOP),
            new AdwordsSectionParser(AdwordsSectionParser::ADS_SECTION_BOTTOM_XPATH, AdwordsResultType::SECTION_BOTTOM)
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

<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\NaturalResultType;

class ResultsNo implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{
    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->getAttribute('id') == 'result-stats') {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $resultsText = str_replace([',', '.'], '', $node->firstChild->nodeValue);
        $resultsText = preg_replace( '/[^0-9]/', '', $resultsText );
        $resultsNo = (int)$resultsText;

        if ($resultsNo > 0) {
            $resultSet->addItem(new BaseResult(NaturalResultType::RESULTS_NO, [$resultsNo]));
        }
    }
}

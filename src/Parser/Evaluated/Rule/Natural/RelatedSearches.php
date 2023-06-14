<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Page\GoogleDom;

class RelatedSearches implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{
    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->getAttribute('id') == 'bres') {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $urlsNodes = $googleDOM->getXpath()->query('descendant::a[contains(concat(\' \', normalize-space(@class), \' \'), \' R0xfCb \')]', $node);
        if ($urlsNodes->length > 0 ) {
            $items = [];
            for ($i = 0; $i < $urlsNodes->length; $i++) {
                if (!empty($urlsNodes->item($i))){
                    $items[] = $urlsNodes->item($i)->getNodeValue();
                }
            }
            if (count($items)) {
                $resultSet->addItem(new BaseResult(NaturalResultType::RELATED_SEARCHES, $items));
            }
        }

    }
}

<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\NaturalResultType;

class FeaturedSnipped implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{
    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->getAttribute('class') == 'xpdopen') {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile=false)
    {
        $naturalResultNode = $googleDOM->getXpath()->query("descendant::div[@class='g']", $node);

        if ($naturalResultNode->length == 0) {
            return;
        }

        $googleDOM->getXpath()->query("descendant::a", $naturalResultNode->item(0));

        $aTag = $googleDOM->getXpath()->query("descendant::a", $naturalResultNode->item(0));

        if ($aTag->length == 0) {
            return;
        }

        $resultSet->addItem(
            new BaseResult(NaturalResultType::FEATURED_SNIPPED,
                [$aTag->item(0)->getAttribute('href')])
        );
    }
}

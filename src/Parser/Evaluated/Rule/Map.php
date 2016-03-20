<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\ResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterace;
use Serps\SearchEngine\Google\NaturalResultType;

class Map implements ParsingRuleInterace
{

    public function match(GoogleDom $dom, \DOMElement $node)
    {
        if ($dom->getXpath()->query("descendant::h3[@class='_Xhb']", $node)) {
            return self::RULE_MATCH_MATCHED;
        }
        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $dom, \DomElement $node, ResultSet $resultSet)
    {
        $item = [];
        $resultSet->addItem(new BaseResult(NaturalResultType::MAP, $item));

        // TODO
    }
}

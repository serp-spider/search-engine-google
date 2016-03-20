<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Raw\Rule;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\ResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterace;
use Serps\SearchEngine\Google\NaturalResultType;

class Map implements ParsingRuleInterace
{

    public function match(GoogleDom $dom, \DOMElement $node)
    {
        if ($node->getAttribute('class') !== 'g _Arj') {
            return ParsingRuleInterace::RULE_MATCH_NOMATCH;
        }

        return ParsingRuleInterace::RULE_MATCH_MATCHED;
    }

    public function parse(GoogleDom $dom, \DomElement $node, ResultSet $resultSet)
    {
        $resultSet->addItem(
            new BaseResult(NaturalResultType::MAP, [
                'snippet' => $node->C14N()
                // TODO
            ])
        );
    }
}

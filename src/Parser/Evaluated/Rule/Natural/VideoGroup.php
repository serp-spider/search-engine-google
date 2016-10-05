<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterace;

/**
 * This rule extracts video groups as present on mobile results
 */
class VideoGroup implements ParsingRuleInterace
{

    public function match(GoogleDom $dom, \DOMElement $node)
    {
        if ($dom->cssQuery('._Fzo', $node)->length == 1) {
            return self::RULE_MATCH_MATCHED;
        }
        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {

        $xPath = $dom->getXpath();

        $item = [

            'items'    => function () use ($node, $dom) {
                return [];
            }

        ];

        $resultSet->addItem(new BaseResult(NaturalResultType::VIDEO_GROUP, $item));
    }
}

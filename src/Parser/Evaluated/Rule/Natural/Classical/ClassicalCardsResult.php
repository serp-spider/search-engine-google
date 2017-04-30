<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Google\NaturalResultType;

class ClassicalCardsResult extends ClassicalResult
{

    public function match(GoogleDom $dom, \DOMElement $node)
    {
        if ($node->getAttribute('class') == 'mnr-c' && null !== $node->childNodes->item(0)) {
            if ($dom->cssQuery('.rc', $node)->length == 1) {
                return self::RULE_MATCH_MATCHED;
            }
        }
        return self::RULE_MATCH_NOMATCH;
    }
}

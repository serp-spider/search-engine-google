<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterace;
use Serps\SearchEngine\Google\NaturalResultType;

class NewDesignClassicalResult extends ClassicalResult
{

    public function match(GoogleDom $dom, \DOMElement $node)
    {
        if ($node->getAttribute('class') == 'mnr-c' && null !== $node->childNodes->item(0)) {
            foreach ($node->childNodes->item(0)->childNodes as $node) {
                if ($node instanceof \DOMElement && $node->getAttribute('class') == 'rc') {
                    return self::RULE_MATCH_MATCHED;
                }
            }
        }
        return self::RULE_MATCH_NOMATCH;
    }
}

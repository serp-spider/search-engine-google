<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule;

use Serps\Core\Serp\ResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterace;

/**
 * This is a group of results that need to be sub-parsed
 */
class SearchResultGroup implements ParsingRuleInterace
{
    public function match(\DOMElement $node)
    {
        if ($node->getAttribute('class') == 'srg') {
            return $node->childNodes;
        } else {
            return self::RULE_MATCH_NOMATCH;
        }
    }

    public function parse(GoogleDom $dom, \DomElement $node, ResultSet $resultSet)
    {
    }
}

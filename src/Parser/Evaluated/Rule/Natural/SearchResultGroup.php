<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;

/**
 * This is a group of results that need to be sub-parsed
 */
class SearchResultGroup implements ParsingRuleInterface
{
    public function match(GoogleDom $dom, \DOMElement $node)
    {
        $class = $node->getAttribute('class');

        if ('srg' == $class || '_NId' == $class) {
            return $node->childNodes;
        } else {
            return self::RULE_MATCH_NOMATCH;
        }
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {
    }
}

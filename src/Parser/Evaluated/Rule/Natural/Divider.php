<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;

class Divider implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        /**
         * Divider should not be parsed and for performance we just skip the parsing
         */
        if ('hr' == $node->tagName || 'rgsep' == $node->getAttribute('class')) {
            return self::RULE_MATCH_STOP;
        }
    }

    public function parse(GoogleDom $googleDOM, \DomElement $group, IndexedResultSet $resultSet)
    {
    }
}

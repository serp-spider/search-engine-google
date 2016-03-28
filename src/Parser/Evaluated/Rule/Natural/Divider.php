<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\ResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterace;

class Divider implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterace
{

    public function match(GoogleDom $dom, \DOMElement $node)
    {
        /**
         * Divider should not be parsed and for performance we just skip the parsing
         */
        if ($node->getAttribute('class') == 'rgsep') {
            return self::RULE_MATCH_STOP;
        }
    }


    public function parse(GoogleDom $googleDOM, \DomElement $group, ResultSet $resultSet)
    {
    }
}

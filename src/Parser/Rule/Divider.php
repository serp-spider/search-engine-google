<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Rule;

use Serps\Core\Serp\ResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;

class Divider implements ParsingRuleInterace
{

    public function match(\DOMElement $node)
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

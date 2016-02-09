<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Rule;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\ResultSet;

interface ParsingRuleInterace
{
    const RULE_MATCH_MATCHED = 1;
    const RULE_MATCH_NOMATCH = 2;
    const RULE_MATCH_STOP = 3;

    public function match(\DOMElement $node);
    public function parse(GoogleDom $dom, \DomElement $node, ResultSet $resultSet);
}

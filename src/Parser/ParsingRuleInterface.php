<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\IndexedResultSet;

interface ParsingRuleInterface
{
    const RULE_MATCH_MATCHED = 1;
    const RULE_MATCH_NOMATCH = 2;
    const RULE_MATCH_STOP = 3;

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node);
    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet);
}

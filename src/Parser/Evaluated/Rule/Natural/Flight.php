<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;

class Flight implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{
    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = false;
    
    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ('flun' == $node->getAttribute('id')) {
            return self::RULE_MATCH_MATCHED;
        }
        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $group, IndexedResultSet $resultSet, $isMobile=false)
    {
        $resultSet->addItem(new BaseResult(NaturalResultType::FLIGHTS, [], $group, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
    }
}

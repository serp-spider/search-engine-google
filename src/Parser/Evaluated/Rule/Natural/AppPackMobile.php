<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\NaturalResultType;

class AppPackMobile implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{
    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = false;
    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->hasClass('qs-io')) {
            // Reason of "instanceof": Avoid parsing "Something went wrong." results
            if(!$node->childNodes[0] instanceof \DOMText && $node->childNodes[0]->hasClass('qs-ii')) {
                return self::RULE_MATCH_MATCHED;
            }
        }

        if ($node->hasClass('ki5rnd') && $node->parentNode->hasAttribute('data-app')) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile=false)
    {
        $resultSet->addItem(
            new BaseResult(NaturalResultType::APP_PACK_MOBILE, [], $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition)
        );
    }
}

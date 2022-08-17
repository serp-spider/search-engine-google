<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Dom\DomElement;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\NaturalResultType;

class ProductListing implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{

    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = false;
    
    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->hasClass('commercial-unit-desktop-top') || $node->hasClass('cu-container')) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile=false)
    {
        $productsNodes = $googleDOM->getXpath()->query("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' pla-unit ') or
        contains(concat(' ', normalize-space(@class), ' '), ' mnr-c ') ]", $node);
        $items         = [];

        if ($productsNodes->length == 0) {
            return;
        }

        foreach ($productsNodes as $productNode) {
            $aHrefProduct = $productNode->childNodes[1];

            if (!$aHrefProduct instanceof DomElement) {
                continue;
            }

            if ($aHrefProduct->getTagName() != 'a') {
                continue;
            }

            $productUrl = $aHrefProduct->getAttribute('href');
            $items[]    = ['url' => $productUrl];
        }

        if (!empty($items)) {
            $resultSet->addItem(
                new BaseResult(NaturalResultType::PRODUCT_LISTING, $items, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition)
            );
        }
    }
}

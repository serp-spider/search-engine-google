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
        if (str_contains($node->getAttribute('class'),  'commercial-unit-desktop-top') || str_contains($node->getAttribute('class'),  'cu-container') || $node->getAttribute('data-enable-product-traversal') == true) {
            if (str_contains($node->getAttribute('class'), 'cu-container')) {
                //$this->hasSideSerpFeaturePosition = true;
                $this->checkIfSidePosition($node);
            }
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile=false)
    {
        $productsNodes = $googleDOM->getXpath()->query("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' pla-unit ') or
        contains(concat(' ', normalize-space(@class), ' '), ' mnr-c ')]", $node);
        $items         = [];

        if ($productsNodes->length == 0) {

            $productsNodes = $googleDOM->getXpath()->query("descendant::li[contains(concat(' ', normalize-space(@data-offer-surface), ' '), ' search-result-surface ')]", $node);
            if ($productsNodes->length == 0) {
                return;
            }
        }

        foreach ($productsNodes as $productNode) {
            $aHrefProduct = $productNode->childNodes[1];
            if (!empty($aHrefProduct) && $aHrefProduct->getTagName() != 'a') {
                $aHrefProduct = $productNode->childNodes[0];
            }
            $seller = false;
            if (!$aHrefProduct instanceof DomElement || (!empty($aHrefProduct) && $aHrefProduct->getTagName() != 'a')) {
                $seller = $googleDOM->getXpath()->query("descendant::span[@class='WJMUdc rw5ecc']", $productNode)->item(0);
                if (empty($seller)) {
                    continue;
                }
            }
            if (!$seller) {
                $productUrl = $aHrefProduct->getAttribute('href');
            } else {
                $productUrl = explode('-',$seller->textContent)[0];
            }

            $items[]    = ['url' => $productUrl];
        }

        if (!empty($items)) {
            $resultSet->addItem(
                new BaseResult(NaturalResultType::PRODUCT_LISTING, $items, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition)
            );
        }
    }

    private function checkIfSidePosition(\Serps\Core\Dom\DomElement $node) {
        //this could be used for all side position elements
        if ($node->getAttribute('id') === 'center_col') {
            //item is in results list
            return false;
        }
        while ($node->parentNode !== null) {
            if ($node->parentNode instanceof \DOMDocument) {
                break;
            }

            if ( $node->parentNode->getAttribute('id') === 'center_col') {
                //item is in results list
                break;
            }

            if ($node->parentNode->getAttribute('role') === 'complementary') {
                $this->hasSideSerpFeaturePosition = true;
            }

            $node = $node->parentNode;
        }
    }
}

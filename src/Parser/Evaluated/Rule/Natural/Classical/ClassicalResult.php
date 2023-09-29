<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical;

use Serps\Core\Dom\DomElement;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\AbstractRuleDesktop;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SiteLinksBig;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SiteLinksSmall;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Google\NaturalResultType;

class ClassicalResult extends AbstractRuleDesktop implements ParsingRuleInterface
{
    public function match(GoogleDom $dom, DomElement $node)
    {
        if ($node->getAttribute('id') == 'rso') {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    protected function parseNode(GoogleDom $dom, \DomElement $organicResult, IndexedResultSet $resultSet, $k)
    {
        $this->parseNodeWithRules($dom, $organicResult, $resultSet, $k);

        if( $dom->xpathQuery("descendant::table[@class='jmjoTe']", $organicResult)->length >0) {
            (new SiteLinksBig())->parse($dom,$organicResult, $resultSet, false);
        }

        $parentWithSameClass = $dom->xpathQuery("ancestor::div[@class='g']", $organicResult);


        if($parentWithSameClass->length > 0) {
            if( $dom->xpathQuery("descendant::table[@class='jmjoTe']", $parentWithSameClass->item(0))->length >0) {
                (new SiteLinksBig())->parse($dom,$parentWithSameClass->item(0), $resultSet, false);
            }
        }

        if( $dom->xpathQuery("descendant::div[@class='HiHjCd']", $organicResult)->length >0) {
            (new SiteLinksSmall())->parse($dom,$organicResult, $resultSet, false);
        }

        if($parentWithSameClass->length > 0) {
            if( $dom->xpathQuery("descendant::div[@class='HiHjCd']", $parentWithSameClass->item(0))->length >0) {
                (new SiteLinksBig())->parse($dom,$parentWithSameClass->item(0), $resultSet, false);
            }
        }
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $naturalResults = $dom->xpathQuery("descendant::*[contains(concat(' ', normalize-space(@class), ' '), ' g ') or contains(concat(' ', normalize-space(@class), ' '), ' MYVUIe ')]", $node);

        if ($naturalResults->length == 0) {

            $resultSet->addItem(new BaseResult(NaturalResultType::EXCEPTIONS, [], $node));
            $this->monolog->error('Cannot identify results in html page', ['class' => self::class]);

            return;
        }

        $k=0;

        foreach ($naturalResults as $organicResult) {

            if($this->skiResult($dom,$organicResult)) {
                continue;
            }

            $k++;
            $this->parseNode($dom, $organicResult, $resultSet, $k);
        }

    }

    protected function skiResult(GoogleDom $googleDOM, DomElement $organicResult)
    {

        // Organic result is identified as top ads
        if($googleDOM->xpathQuery("ancestor::*[contains(concat(' ', normalize-space(@id), ' '), ' tads')]", $organicResult)->length > 0) {
            return true;
        }

        if ($googleDOM->xpathQuery("ancestor::g-accordion-expander ", $organicResult)->length >0) {
            return true;
        }

        if($googleDOM->xpathQuery("ancestor::*[contains(concat(' ', normalize-space(@id), ' '), ' tvcap ')]", $organicResult)->length > 0) {
            return true;
        }

        // Recipes are identified as organic result
        if ($organicResult->getChildren()->hasClasses(['rrecc'])) {
            return true;
        }

        // This result is a featured snipped. It it has another div with class g that contains organic results -> avoid duplicates
        if( $organicResult->hasClasses(['mnr-c'])) {
            return true;
        }

        if( $organicResult->hasClasses(['g-blk'])) {
            return true;
        }
        
        $fsnParent =   $googleDOM->getXpath()->query("ancestor::div[contains(concat(' ', normalize-space(@class), ' '), ' xpdopen ')]", $organicResult);

        if ($fsnParent->length > 0) {

            return true;
        }


        // Avoid getting  results from questions (when clicking "Show more". When clicking "Show more" on questions)
        // The result under it looks exactly like a natural results
        $questionParent =   $googleDOM->getXpath()->query("ancestor::div[contains(concat(' ', normalize-space(@class), ' '), ' related-question-pair ')]", $organicResult);

        if ($questionParent->length > 0) {

            return true;
        }

        $hasParentG     = $googleDOM->getXpath()->query("ancestor::div[contains(concat(' ', normalize-space(@class), ' '), ' g ')]", $organicResult);
        $hasParentFxLDp = $googleDOM->getXpath()->query("ancestor::ul[contains(concat(' ', normalize-space(@class), ' '), ' FxLDp ')]", $organicResult);
        $hasSameChild  = false;

        if ($organicResult->hasClasses(['MYVUIe'])) {
            $hasChildG = $googleDOM->getXpath()->query("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' g ')]", $organicResult);

            if ($hasChildG->length > 0) {
                $hasSameChild = true;
            }
        }

        if ( ($hasParentG->length > 0 && $hasParentFxLDp->length ==0)  || $hasSameChild) {
            return true;
        }

        $hasChildKp = $googleDOM->getXpath()->query("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' kp-wholepage ')]", $organicResult);

        if ($hasChildKp->length > 0) {
            return true;
        }
        //
        $currencyPlayer = $googleDOM->getXpath()->query('descendant::div[@id="knowledge-currency__updatable-data-column"]', $organicResult);

        if($currencyPlayer->length>0) {
            return true;
        }

        return false;
    }
}
//

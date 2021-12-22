<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical;

use Serps\Core\Dom\DomElement;
use Serps\SearchEngine\Google\Exception\InvalidDOMException;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\AbstractRuleDesktop;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SiteLinks;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SiteLinksBig;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SiteLinksBigMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SiteLinksSmall;
use Serps\SearchEngine\Google\Parser\ParsingRuleByVersionInterface;
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

        if( $dom->xpathQuery("descendant::div[@class='HiHjCd']", $organicResult)->length >0) {
            (new SiteLinksSmall())->parse($dom,$organicResult, $resultSet, false);
        }
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $naturalResults = $dom->xpathQuery("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' g ')]", $node);

        if ($naturalResults->length == 0) {

            $resultSet->addItem(new BaseResult(NaturalResultType::EXCEPTIONS, []));
            $this->monolog->error('Cannot identify results in html page ', ['html'=>$node->ownerDocument->saveHTML($node), ]);

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
        // Recipes are identified as organic result
        if ($organicResult->getChildren()->hasClasses(['rrecc'])) {
            return true;
        }

        // This result is a featured snipped. It it has another div with class g that contains organic results -> avoid duplicates
        if( $organicResult->hasClasses(['mnr-c'])) {
            return true;
        }

        // Avoid getting  results from questions (when clicking "Show more". When clicking "Show more" on questions)
        // The result under it looks exactly like a natural results
        $node = $organicResult->parentNode->parentNode;

        if ($node->parentNode->getAttribute('class') =='ymu2Hb' ||
            $node->getAttribute('class') =='g') {

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

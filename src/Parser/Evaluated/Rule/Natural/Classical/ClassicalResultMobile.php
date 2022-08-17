<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical;

use Serps\Core\Dom\DomElement;
use Serps\Core\Dom\DomNodeList;
use Serps\SearchEngine\Google\Exception\InvalidDOMException;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\AbstractRuleMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SiteLinksBigMobile;
use Serps\SearchEngine\Google\Parser\ParsingRuleByVersionInterface;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Google\NaturalResultType;

class ClassicalResultMobile extends AbstractRuleMobile implements ParsingRuleInterface
{
    protected $resultType = NaturalResultType::CLASSICAL_MOBILE;

    public function match(GoogleDom $dom, DomElement $node)
    {
        if ($node->getAttribute('id') == 'center_col' || $node->getAttribute('id') =='sports-app') {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    protected function parseNode(GoogleDom $dom, \DomElement $organicResult, IndexedResultSet $resultSet, $k)
    {
        $this->parseNodeWithRules($dom, $organicResult, $resultSet, $k);

        if ($dom->xpathQuery("descendant::div[@class='MUxGbd v0nnCb lyLwlc']",
                $organicResult->parentNode->parentNode)->length > 0) {
            (new SiteLinksBigMobile())->parse($dom, $organicResult->parentNode->parentNode, $resultSet, false);
        }
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $naturalResults = $dom->xpathQuery("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' mnr-c ') or contains(concat(' ', normalize-space(@class), ' '), ' Ww4FFb ')]", $node);

        if ($naturalResults->length == 0) {
            $resultSet->addItem(new BaseResult(NaturalResultType::EXCEPTIONS, [], $node));
            $this->monolog->error('Cannot identify results in html page', ['class' => self::class]);

            return;
        }

        $k = 0;

        foreach ($naturalResults as $organicResult) {

            if ($this->skiResult($dom, $organicResult)) {
                continue;
            }

            try {
                $k++;
                $this->parseNode($dom, $organicResult, $resultSet, $k);
            } catch (\Exception $exception) {

                // If first position detected with classical class it's not a results, do not decrement position
                if ($k > 1) {
                    $k--;
                }

                continue;
            }
        }

    }

    protected function skiResult(GoogleDom $dom, DomElement $organicResult)
    {
        // Organic result is identified as top ads
        if($dom->xpathQuery("ancestor::*[contains(concat(' ', normalize-space(@id), ' '), ' tads')]", $organicResult)->length > 0) {
            return true;
        }

        if($dom->xpathQuery("ancestor::*[contains(concat(' ', normalize-space(@id), ' '), ' tvcap ')]", $organicResult)->length > 0) {
            return true;
        }
        // Knowledge graph, sometimes, is identified as an organic result
        if ($organicResult->hasClasses(['kp-wholepage'])) {
            return true;
        }

        // Recipes are identified as organic result
        if ($organicResult->getChildren()->hasClasses(['Q9mvUc'])) {
            return true;
        }

        if($dom->xpathQuery("ancestor::*[contains(concat(' ', normalize-space(@id), ' '), '  mnr-c ')]", $organicResult)->length > 0) {
            return true;
        }
        // Inside div with class= 'mnr-c xpd O9g5cc uUPGi' are more divs with 'mnr-c xpd O9g5cc uUPGi'
        // Should ignore from processing parent result and process only children and avoid duplicate results
        if($dom->xpathQuery("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' mnr-c ')]", $organicResult)->length >0) {
            return true;
        }

        // Ignore maps from results
        if($dom->xpathQuery("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' z3HNkc ')]", $organicResult)->length >0) {
            return true;
        }

        $questionParent =   $dom->getXpath()->query("ancestor::div[contains(concat(' ', normalize-space(@class), ' '), ' related-question-pair ')]", $organicResult);

        if ($questionParent->length > 0) {

            return true;
        }

        // Avoid getting  results from questions (when clicking "Show more". When clicking "Show more" on questions)
        // The result under it looks exactly like a natural results
        if(
            $organicResult->parentNode->parentNode->parentNode->getAttribute('class') =='ymu2Hb' ||
            $organicResult->parentNode->parentNode->parentNode->getAttribute('class') =='dfiEbb' ||
            $organicResult->parentNode->parentNode->parentNode->parentNode->getAttribute('class') =='ymu2Hb') {

            return true;
        }

        // The organic result identified as "Find results on"

        $carouselNode = $dom->xpathQuery("descendant::g-scrolling-carousel", $organicResult);
        if ($carouselNode->length > 0 &&
            $dom->xpathQuery("descendant::g-inner-card", $organicResult)->length > 0) {

            // If the  direct parent of the carousel is the class from classical results -> meaning that there is no classical result here to be parsed.
            // If the  direct parent of the carousel is NOT the class from classical results -> this is a classical result and under it is a carousel. need to parse the node and identify title/url/description
            if(preg_match('/mnr\-c/', $carouselNode->item(0)->parentNode->getAttribute('class'))) {
                return true;
            }
        }

        // Result has carousel in it
        if ($dom->xpathQuery("descendant::g-scrolling-carousel", $organicResult)->length > 0 &&
            $dom->xpathQuery("descendant::svg", $organicResult)->length > 0 &&
            (  // And carousel have title like "Results in "
                $dom->getXpath()->query("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' pxp6I MUxGbd ')]", $organicResult)->length > 0 ||
                // Temporary keep this
                $dom->getXpath()->query("descendant::table", $organicResult)->length > 0
            )) {
            return true;
        }

        // Avoid getting results  such as "people also ask" near a regular result; (it's not a "people also ask" but the functionality is exactly like "people also ask")
        // It's like an expander with click on a main text. The results under it looks like a regular classical result
        if( !empty($organicResult->firstChild) &&
            !$organicResult->firstChild instanceof \DOMText &&
            $organicResult->firstChild->getAttribute('class') =='g card-section') {

            return true;
        }

        return false;
    }
}

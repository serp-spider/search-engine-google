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
        if ($node->getAttribute('id') == 'rso') {
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
        $naturalResults = $dom->xpathQuery("descendant::div[@class='mnr-c xpd O9g5cc uUPGi']", $node);

        if ($naturalResults->length == 0) {
            throw new InvalidDOMException('Cannot parse a classical result.');
        }

        $k = 0;

        foreach ($naturalResults as $organicResult) {

            if ($this->skiResult($dom, $organicResult)) {
                continue;
            }

            $k++;

            $this->parseNode($dom, $organicResult, $resultSet, $k);
        }

    }

    protected function skiResult(GoogleDom $dom, DomElement $organicResult)
    {
        // Recipes are identified as organic result
        if ($organicResult->getChildren()->hasClasses(['Q9mvUc'])) {
            return true;
        }

        if ($dom->xpathQuery("descendant::g-scrolling-carousel", $organicResult)->length > 0) {
            return true;
        }

        return false;
    }
}

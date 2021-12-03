<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Dom\DomElement;
use Serps\Core\Dom\DomNodeList;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\NaturalResultType;

class TopStories implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{
    protected $steps = ['version1', 'version2'];

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {

        if (($node->parentNode->hasAttribute('jscontroller') &&
                $node->parentNode->getAttribute('jscontroller') == 'QE1bwd' &&
                $node->parentNode->tagName == 'g-expandable-container') ||
            ($node->tagName == 'g-section-with-header' && $node->hasClass('yG4QQe'))

        ) {
            return self::RULE_MATCH_MATCHED;
        }

        if(!empty($node->firstChild) &&  !empty($node->firstChild->tagName) && $node->firstChild->tagName == 'g-section-with-header' && $node->firstChild->hasClass('yG4QQe')) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        foreach ($this->steps as $functionName) {
            call_user_func_array([$this, $functionName], [$googleDOM, $node, $resultSet, $isMobile]);
        }
    }

    protected function version1(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile)
    {
        $stories = $googleDOM->getXpath()->query('descendant::g-inner-card', $node);
        $items   = [];

        if ($stories->length == 0) {
            return;
        }

        foreach ($stories as $urlNode) {
            $aNode = $googleDOM->getXpath()->query('descendant::a', $urlNode);

            if ($aNode instanceof DomNodeList && $aNode->length > 0) {
                $link            = $aNode->item(0)->getAttribute('href');
                $items['news'][] = ['url' => $link];
            }
        }

        if (!empty($items)) {
            $resultSet->addItem(
                new BaseResult($this->getType($isMobile), $items)
            );
        }
    }

    protected function getType($isMobile)
    {
        return $isMobile ? NaturalResultType::TOP_STORIES_MOBILE : NaturalResultType::TOP_STORIES;
    }

    protected function version2(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile)
    {
        $hrefsNodes = $googleDOM->getXpath()->query("descendant::a[@class='WlydOe']", $node);

        if (!$hrefsNodes instanceof DomNodeList) {
            return;
        }

        if ($hrefsNodes->length == 0) {
            return;
        }

        $items = [];

        foreach ($hrefsNodes as $hrefNode) {
            /** @var $hrefNode DomElement */
            $items['news'][] = ['url' => $hrefNode->getAttribute('href')];
        }

        if (!empty($items)) {
            $resultSet->addItem(new BaseResult($this->getType($isMobile), $items));
        }
    }
}

<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\NaturalResultType;

class Questions implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{
    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = false;

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->hasClass('related-question-pair')) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    protected function getType($isMobile)
    {
        return $isMobile ? NaturalResultType::QUESTIONS_MOBILE : NaturalResultType::QUESTIONS;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $urlsNodes  = $googleDOM->getXpath()->query('descendant::a', $node);
        $qTextNodes = $googleDOM->getXpath()->query('descendant::span', $node);
        $firstUrl = '';
        $qText = '';
        if ($urlsNodes->length > 0) {
            $firstUrl = $urlsNodes->item(0)->getAttribute('href');
        }
        if ($qTextNodes->length > 0) {
            $qText = $qTextNodes->item(0)->getNodeValue();
        }
        $resultSet->addItem(
            new BaseResult($this->getType($isMobile), ['url' => $firstUrl, 'title' => $qText], $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition)
        );

    }
}

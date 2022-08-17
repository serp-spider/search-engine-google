<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Dom\DomElement;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Google\NaturalResultType;

class Maps implements ParsingRuleInterface
{
    protected $steps = ['version1', 'version2', 'version3'];
    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = false;

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->getAttribute('class') == 'C7r6Ue' || str_contains($node->getAttribute('class'),  'WVGKWb')) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }


    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        foreach ($this->steps as $functionName) {

            if ($resultSet->hasType(NaturalResultType::MAP)) {
                break 1;
            }

            try {
                call_user_func_array([$this, $functionName], [$googleDOM, $node, $resultSet, $isMobile]);
            } catch (\Exception $exception) {
                continue;
            }

        }
    }

    protected function version2(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile)
    {
        $ratingStars = $googleDOM->getXpath()->query("descendant::div[@class='rllt__details']", $node);

        if ($ratingStars->length == 0) {
            return;
        }

        foreach ($ratingStars as $ratingStarNode) {
            if (empty($ratingStarNode->parentNode->childNodes[1])) {
                continue;
            }

            $spanElements['title'][] = $ratingStarNode->parentNode->childNodes[1]->textContent;
        }

        if(!empty($spanElements)) {
            $resultSet->addItem(new BaseResult(NaturalResultType::MAP, $spanElements, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
        }
    }

    protected function version3(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile)
    {
        $ratingStars = $googleDOM->getXpath()->query("descendant::div[@class='rllt__details']", $node);

        if ($ratingStars->length == 0) {
            return;
        }

        foreach ($ratingStars as $ratingStarNode) {
            if($ratingStarNode->childNodes->length ==0) {
                continue;
            }

            $spanElements['title'][] =  $ratingStarNode->childNodes->item(0)->textContent;
        }

        if(!empty($spanElements)) {
            $resultSet->addItem(new BaseResult(NaturalResultType::MAP, $spanElements, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
        }
    }


    protected function version1(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile)
    {
        $ratingStars = $googleDOM->getXpath()->query('descendant::g-review-stars', $node);

        if ($ratingStars->length == 0) {
            return;
        }

        $spanElements = [];

        foreach ($ratingStars as $ratingStarNode) {
            $spanElements['title'][] = $ratingStarNode->parentNode->parentNode->parentNode->childNodes[1]
                ->childNodes[0]->textContent;
        }

        $resultSet->addItem(new BaseResult(NaturalResultType::MAP, $spanElements, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
    }
}

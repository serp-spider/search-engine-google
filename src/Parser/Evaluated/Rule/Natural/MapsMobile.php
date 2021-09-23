<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Google\NaturalResultType;

class MapsMobile implements ParsingRuleInterface
{

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->hasClass('scm-c')) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet)
    {
        $ratingStars = $googleDOM->getXpath()->query('descendant::g-review-stars', $node);

        if ($ratingStars->length == 0) {
            return;
        }

        $spanElements = [];

        foreach ($ratingStars as $ratingStarNode) {
            $spanElements['title'][] = $ratingStarNode->parentNode->parentNode->childNodes[0]->childNodes[0]->textContent;
        }

        if($this->isMobile) {

        }
    }
}

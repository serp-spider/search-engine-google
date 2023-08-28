<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\NaturalResultType;

class Recipes implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{
    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = false;

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if (strpos($node->getAttribute('jsname'), 'MGJTwe') !== false) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $urls = $googleDOM->getXpath()->query('descendant::g-link', $node);
        $item = [];
        $urlOnAttribute  = false;
        if ($urls->length == 0) {
            $urlOnAttribute =  true;
            $urls = $googleDOM->getXpath()->query('descendant::a[@data-rl]', $node);
        }
        if ($urls->length > 0) {
            foreach ($urls as $urlNode) {
                if ($urlOnAttribute) {
                    $item['recipes_links'][] = ['link' => $urlNode->getAttribute('data-rl')];
                } else {
                    $item['recipes_links'][] = ['link' => $urlNode->firstChild->getAttribute('href')];
                }

            }

            $resultSet->addItem(new BaseResult(NaturalResultType::RECIPES_GROUP , $item, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
        }
    }
}

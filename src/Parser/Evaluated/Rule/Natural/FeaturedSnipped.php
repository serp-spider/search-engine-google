<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\NaturalResultType;

class FeaturedSnipped implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{
    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = false;

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->getAttribute('class') == 'xpdopen') {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    protected function getType($isMobile)
    {
        return $isMobile ? NaturalResultType::FEATURED_SNIPPED_MOBILE : NaturalResultType::FEATURED_SNIPPED;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $naturalResultNodes = $googleDOM->getXpath()->query("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' g ')]", $node);

        if ($naturalResultNodes->length == 0) {
            return;
        }

        $results = [];

        foreach ($naturalResultNodes  as $featureSnippetNode) {
            $isHidden = $googleDOM->getXpath()->query("ancestor::g-accordion-expander", $featureSnippetNode);
            if ($isHidden->length >  0) {
                continue;
            }

            $aTag = $googleDOM->getXpath()->query("descendant::a", $featureSnippetNode);
            $h3Tag = $googleDOM->getXpath()->query("descendant::h3", $featureSnippetNode);//title
            $description = $googleDOM->getXpath()->query("preceding-sibling::div/descendant::div[@class='LGOjhe']", $featureSnippetNode);//description

            if ($aTag->length == 0) {
                continue;
            }

            $object              = new \StdClass();
            $object->url         = $aTag->item(0)->getAttribute('href');
            $object->description = (!empty($description) && !empty($description->item(0)) && !empty($description->item(0)->textContent)) ? $description->item(0)->textContent : '';
            $object->title       = (!empty($h3Tag) && !empty($h3Tag->item(0)) && !empty($h3Tag->item(0)->textContent)) ? $h3Tag->item(0)->textContent : '';

            $results[] = $object;
        }

        if(!empty($results)) {
            $resultSet->addItem(
                new BaseResult($this->getType($isMobile), $results, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition)
            );
        }
    }
}

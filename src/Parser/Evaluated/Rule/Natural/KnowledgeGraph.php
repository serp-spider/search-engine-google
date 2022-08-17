<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Page\GoogleDom;

class KnowledgeGraph implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{
    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = true;

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($dom->cssQuery('.kp-wholepage.kp-wholepage-osrp', $node)->length == 1) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    protected function getType($isMobile)
    {
        return $isMobile ? NaturalResultType::KNOWLEDGE_GRAPH_MOBILE : NaturalResultType::KNOWLEDGE_GRAPH;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $group, IndexedResultSet $resultSet, $isMobile = false)
    {
        $data = [];

        /** @var \DomElement $titleNode */
        $titleNode = $googleDOM->cssQuery("div[data-attrid='subtitle']", $group)->item(0);

        if ($titleNode instanceof \DomElement) {
            $data['title'] = $titleNode->textContent;
        } else {
            $titleNode = $googleDOM->getXpath()->query("descendant::h2[contains(concat(' ', normalize-space(@class), ' '), ' kno-ecr-pt ')]", $group);

            if($titleNode->length >0) {
                $data['title'] = $titleNode->item(0)->firstChild->textContent;
            }
        }

        // Has no definition -> take "general presentation" text
        if(empty($data)) {
            $data['title']= $this->detectGeneralPresentationText($googleDOM, $group);
        }

        if ($isMobile) {
            $this->hasSideSerpFeaturePosition = false;
        }

        $resultSet->addItem(new BaseResult($this->getType($isMobile), $data, $group, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
    }

    protected function detectGeneralPresentationText(GoogleDom $googleDOM, \DomElement $group)
    {
        $aHrefs = $googleDOM->getXpath()->query("descendant::a[contains(concat(' ', normalize-space(@class), ' '), ' KYeOtb ')]", $group);

        if ($aHrefs->length > 0) {
            return $aHrefs->item(0)->textContent;
        }

        $title = $googleDOM->getXpath()->query("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' BkwXh ')]", $group);

        if ($title->length > 0) {
            return $title->item(0)->textContent;
        }

        return '';
    }
}

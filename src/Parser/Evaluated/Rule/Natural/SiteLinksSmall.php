<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\NaturalResultType;

class SiteLinksSmall implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{

    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = false;

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        return self::RULE_MATCH_MATCHED;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $siteLinksNodes = $googleDOM->xpathQuery("descendant::div[@class='HiHjCd']/a", $node);

        if ($siteLinksNodes->length == 0) {
            return;
        }

        $items = [];

        foreach ($siteLinksNodes as $aNode) {
            $items[] = ['title' => $aNode->textContent, 'url' => $aNode->getAttribute('href')];
        }

        $resultSet->addItem(
            new BaseResult(NaturalResultType::SITE_LINKS_SMALL, $items, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition)
        );
    }
}

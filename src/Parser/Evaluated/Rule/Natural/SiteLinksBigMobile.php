<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\NaturalResultType;

class SiteLinksBigMobile implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{

    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = false;

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        return self::RULE_MATCH_MATCHED;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $siteLinksNodes = $googleDOM->xpathQuery("descendant::div[@class='MUxGbd v0nnCb lyLwlc']", $node);

        if ($siteLinksNodes->length == 0) {
            return;
        }

        $items = [];

        foreach ($siteLinksNodes as $siteLinksNode) {
            $aNode   = $googleDOM->xpathQuery("descendant::a", $siteLinksNode)->item(0);
            $items[] = ['title' => $aNode->textContent, 'url' => $aNode->getAttribute('href')];
        }

        $resultSet->addItem(
            new BaseResult(NaturalResultType::SITE_LINKS_BIG_MOBILE, $items, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition)
        );
    }
}

<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Media\MediaFactory;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\Core\UrlArchive;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Google\NaturalResultType;

class AdsTop implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{
    const ADS_TOP_CLASS  = 'tads';
    const ADS_DOWN_CLASS = 'tadsb';
    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = false;

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->getTagName() != 'div') {
            return self::RULE_MATCH_NOMATCH;
        }

        if ($node->getAttribute('id') == self::ADS_TOP_CLASS || // Ads top
            $node->getAttribute('id') == self::ADS_DOWN_CLASS  || // Ads bottom
            $node->getAttribute('id') == 'bottomads' // Ads bottom
        ) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile=false)
    {
        $adsNodes = $googleDOM->getXpath()->query('descendant::a', $node);
        $links    = [];

        if ($adsNodes->length == 0) {
            return;
        }

        foreach ($adsNodes as $adsNode) {
            $link = false;
            if (!$adsNode->hasClass('Krnil')) {
                $linkNodes = $googleDOM->getXpath()->query('descendant::span[contains(concat(\' \', normalize-space(@role), \' \'), \' text \')]', $adsNode);
                if ($linkNodes->length == 0) {
                    continue;
                }
                $link = $linkNodes->item(0)->textContent;

            } else {
                $link = $adsNode->getAttribute('href');
            }

            if (empty($link)) {
                continue;
            }
            $links[] = ['url' => $link];
        }

        if (!empty($links)) {

            if ($node->getAttribute('id') == self::ADS_TOP_CLASS) {
                $resultSet->addItem(new BaseResult(NaturalResultType::AdsTOP, $links, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
            }

            if ($node->getAttribute('id') == self::ADS_DOWN_CLASS) {
                $resultSet->addItem(new BaseResult(NaturalResultType::AdsDOWN, $links, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
            }
        }
    }
}

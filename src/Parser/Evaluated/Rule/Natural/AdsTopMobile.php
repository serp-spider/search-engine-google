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

class AdsTopMobile extends AdsTop
{

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $adsNodes = $googleDOM->getXpath()->query("descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' mnr-c ')]",
            $node);
        $links    = [];

        if ($adsNodes->length == 0) {
            return;
        }

        foreach ($adsNodes as $adsNode) {

            $aHrefs = $googleDOM->getXpath()->query("descendant::a", $adsNode);

            foreach ($aHrefs as $href) {

                if ($href->hasClass('gsrt')) {
                    continue;
                }

                if (preg_match('/googleadservices/', $href->getAttribute('href'))) {
                    continue;
                }

                $links[] = ['url' => $href->getAttribute('href')];
            }
        }

        if (!empty($links)) {

            if ($node->getAttribute('id') == self::ADS_TOP_CLASS) {
                $resultSet->addItem(new BaseResult(NaturalResultType::AdsTOP_MOBILE, $links, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
            }

            if ($node->getAttribute('id') == self::ADS_DOWN_CLASS) {
                $resultSet->addItem(new BaseResult(NaturalResultType::AdsDOWN_MOBILE, $links, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
            }
        }
    }
}

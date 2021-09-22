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

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->getTagName() != 'div') {
            return self::RULE_MATCH_NOMATCH;
        }

        if ($node->getAttribute('id') == self::ADS_TOP_CLASS || // Ads top
            $node->getAttribute('id') == self::ADS_DOWN_CLASS // Ads bottom
        ) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet)
    {
        $adsNodes = $googleDOM->getXpath()->query('descendant::a', $node);
        $links    = [];

        if ($adsNodes->length == 0) {
            return;
        }

        foreach ($adsNodes as $adsNode) {

            if (!$adsNode->hasClass('Krnil')) {
                continue;
            }

            $links[] = ['url' => $adsNode->getAttribute('href')];
        }

        if (!empty($links)) {

            if ($node->getAttribute('id') == self::ADS_TOP_CLASS) {
                $resultSet->addItem(new BaseResult(NaturalResultType::AdsTop, $links));
            }

            if ($node->getAttribute('id') == self::ADS_DOWN_CLASS) {
                $resultSet->addItem(new BaseResult(NaturalResultType::AdsDOWN, $links));
            }
        }
    }
}

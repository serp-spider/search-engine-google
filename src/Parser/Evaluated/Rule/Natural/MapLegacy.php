<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\Core\UrlArchive;
use Serps\SearchEngine\Google\GoogleUrlArchive;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Google\NaturalResultType;

/**
 * This is kept for BC reasons, to be removed in the future
 * TODO
 * @deprecated
 */
class MapLegacy implements ParsingRuleInterface
{

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($dom->cssQuery('._RBh', $node)->length  > 1) {
            return self::RULE_MATCH_MATCHED;
        }
        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {

        $xPath = $dom->getXpath();

        $item = [
            'localPack' => function () use ($xPath, $node, $dom) {
                $localPackNodes = $xPath->query('descendant::div[@class="_gt"]', $node);
                $data = [];
                foreach ($localPackNodes as $localPack) {
                    $data[] = new BaseResult(NaturalResultType::MAP_PLACE, $this->parseItem($localPack, $dom));
                }
                return $data;
            },
            'mapUrl'    => function () use ($xPath, $node, $dom) {
                $mapATag = $dom->cssQuery('#lu_map', $node)->item(0)->parentNode;
                if ($mapATag) {
                    return $dom->getUrl()->resolveAsString($mapATag->getAttribute('href'));
                }
                return null;
            }

        ];

        $resultSet->addItem(new BaseResult(NaturalResultType::MAP, $item));
    }

    private function parseItem($localPack, GoogleDom $dom)
    {

        return [
            'title' => function () use ($localPack, $dom) {
                $item = $dom->cssQuery('._rl', $localPack)->item(0);
                if ($item) {
                    return $item->nodeValue;
                }
                return null;
            },
            'url' => function () use ($localPack, $dom) {
                $item = $dom->getXpath()->query('descendant::a', $localPack)->item(1);
                if ($item) {
                    return $item->getAttribute('href');
                }
                return null;
            },
            'street' => function () use ($localPack, $dom) {
                $item = $dom->cssQuery(
                    '._iPk>span.rllt__details>div:nth-child(3)>span',
                    $localPack
                )->item(0);
                if ($item) {
                    return $item->nodeValue;
                }
                return null;
            },

            'stars' => function () use ($localPack, $dom) {
                $item = $dom->cssQuery('._PXi', $localPack)->item(0);
                if ($item) {
                    return $item->nodeValue;
                }
                return null;
            },

            'review' => function () use ($localPack, $dom) {
                $item = $dom->cssQuery(
                    '._iPk>span.rllt__details>div:nth-child(1)',
                    $localPack
                )->item(0);
                if ($item) {
                    if ($item->childNodes->length > 0 && !($item->childNodes->item(0) instanceof \DOMText)) {
                        return null;
                    } else {
                        return trim(explode('·', $item->nodeValue)[0]);
                    }
                }
                return null;
            },

            'phone' => function () use ($localPack, $dom) {
                $item = $dom->cssQuery(
                    '._iPk>span.rllt__details>div:nth-child(3)',
                    $localPack
                )->item(0);
                if ($item) {
                    if ($item->childNodes->length > 1 && $item->childNodes->item(1) instanceof \DOMText) {
                        return trim($item->childNodes->item(1)->nodeValue, ' ·');
                    }
                }
                return null;
            },
        ];
    }
}

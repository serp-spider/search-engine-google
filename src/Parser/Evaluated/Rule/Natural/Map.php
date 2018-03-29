<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Dom\DomElement;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Google\NaturalResultType;

class Map implements ParsingRuleInterface
{

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($dom->cssQuery('.AEprdc.vk_c', $node)->length == 1) {
            return self::RULE_MATCH_MATCHED;
        }
        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {

        $item = [
            'localPack' => function () use ($node, $dom) {
                $localPackNodes = $dom->cssQuery('.ccBEnf>div', $node);
                $data = [];
                foreach ($localPackNodes as $localPack) {
                    $data[] = new BaseResult(NaturalResultType::MAP_PLACE, $this->parseItem($localPack, $dom));
                }
                return $data;
            },
            'mapUrl'    => function () use ($node, $dom) {
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
                return $dom->cssQuery('.dbg0pd', $localPack)->getNodeAt(0)->getNodeValue();
            },
            'url' => function () use ($localPack, $dom) {

                // we search for explicit <a> with href to the website.
                // if not found the url is sometimes in a <link> tag
                $nodes = $dom->cssQuery('a.L48Cpd');
                if ($nodes->length > 0) {
                    return $nodes->getNodeAt(0)->getAttribute('href');
                } else {
                    return $dom->cssQuery('link[href]', $localPack)
                        ->getNodeAt(0)
                        ->getAttribute('href');
                }
            },
            'street' => function () use ($localPack, $dom) {
                $v = $dom->cssQuery(
                    '.rllt__details>div:nth-child(3)>span',
                    $localPack
                )->getNodeAt(0)->getNodeValue();

                if ($v) {
                    return $v;
                } else {
                    return $dom->cssQuery(
                        '.rllt__details>div:nth-child(1)>span',
                        $localPack
                    )->getNodeAt(0)->getNodeValue();
                }
            },
            'stars' => function () use ($localPack, $dom) {
                $rating = $dom->cssQuery('.BTtC6e', $localPack)->getNodeAt(0)->getNodeValue();

                // transforms "4,4" to 4.4
                return $rating ? (float)str_replace(',', '.', $rating) : null;
            },

            'review' => function () use ($localPack, $dom) {
                $review = $dom->cssQuery(
                    '.BTtC6e',
                    $localPack
                )->getNodeAt(0);

                if ($review instanceof DomElement) {
                    $value = $review->parentNode->getNodeValue();
                } else {
                    return null;
                }

                if ($value && preg_match('/(\([0-9 ,\.]+\))/', $value, $matches)) {
                    // transform '(1 000)' or '(1,000)', etc... to 1000
                    return (int) preg_replace('/[^0-9]/', '', $matches[1]);
                }

                return null;
            },

            'phone' => function () use ($localPack, $dom) {
                $item = $dom->cssQuery(
                    '.rllt__details>div:nth-child(3)',
                    $localPack
                )->item(0);

                if (!$item) {
                    $item = $dom->cssQuery(
                        '.rllt__details>div:nth-child(1)',
                        $localPack
                    )->item(0);
                }

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

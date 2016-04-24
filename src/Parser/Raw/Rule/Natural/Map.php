<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Raw\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\Core\UrlArchive;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterace;
use Serps\SearchEngine\Google\NaturalResultType;

class Map implements ParsingRuleInterace
{

    public function match(GoogleDom $dom, \DOMElement $node)
    {
        if ($node->getAttribute('class') !== 'g _Arj') {
            return ParsingRuleInterace::RULE_MATCH_NOMATCH;
        }

        return ParsingRuleInterace::RULE_MATCH_MATCHED;
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {

        $xPath = $dom->getXpath();

        $resultSet->addItem(
            new BaseResult(NaturalResultType::MAP, [
                'localPack' => function () use ($xPath, $node, $dom) {
                    $localPackNodes = $xPath->query('descendant::div[@class="_Fxi"]', $node);
                    $data = [];
                    foreach ($localPackNodes as $localPack) {
                        $data[] = new BaseResult(NaturalResultType::MAP_PLACE, $this->parseItem($localPack, $dom));
                    }
                    return $data;
                },
                'mapUrl'    => function () use ($xPath, $node, $dom) {
                    $mapATag = $xPath->query('descendant::a[@class="_Tbj"]', $node)->item(0);
                    if ($mapATag) {
                        return $dom->getUrl()->resolve($mapATag->getAttribute('href'), 'string');
                    }
                    return null;
                }
            ])
        );
    }

    private function parseItem(\DOMNode $localPack, GoogleDom $dom)
    {
        return [
            'title' => function () use ($localPack, $dom) {
                $item = $dom->getXpath()->query('descendant::a[@class="_axi"]/div', $localPack)->item(0);
                if ($item) {
                    return $item->nodeValue;
                }
                return null;
            },
            'url' => function () use ($localPack, $dom) {
                $item = $dom->getXpath()->query('descendant::td[@class="_HZj"]/a', $localPack)->item(0);
                if ($item && $href = $item->getAttribute('href')) {
                    if (strpos($href, '/url') === 0) {
                        $url = $dom->getUrl()->resolve($href)->getParamRawValue('q');
                        return $url;
                    }
                }
                return null;
            },
            'street' => function () use ($localPack, $dom) {
                $item = $dom->getXpath()->query(
                    'descendant::a[@class="_axi"]/div[4]/span',
                    $localPack
                )->item(0);
                if ($item) {
                    return $item->nodeValue;
                }
                return null;
            },

            'stars' => function () use ($localPack, $dom) {
                $item = $dom->getXpath()->query('descendant::span[@class="_PXi"]', $localPack)->item(0);
                if ($item) {
                    return $item->nodeValue;
                }
                return null;
            },

            'review' => function () use ($localPack, $dom) {
                $item = $dom->getXpath()->query(
                    'descendant::a[@class="_axi"]/div[2]',
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
        ];
    }
}

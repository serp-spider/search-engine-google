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
use Serps\SearchEngine\Google\Parser\ParsingRuleInterace;
use Serps\SearchEngine\Google\NaturalResultType;

class ImageGroupCarousel implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterace
{

    public function match(GoogleDom $dom, \DOMElement $node)
    {
        if ($dom->cssQuery('._ekh image-viewer-group g-scrolling-carousel', $node)->length == 1) {
            return self::RULE_MATCH_MATCHED;
        } else {
            return self::RULE_MATCH_NOMATCH;
        }
    }
    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet)
    {
        $item = [
            'images' => function () use ($node, $googleDOM) {
                $items = [];

                $imageNodes = $googleDOM->cssQuery('.rg_ul>._sqh g-inner-card', $node);
                foreach ($imageNodes as $imageNode) {
                    $items[] = $this->parseItem($googleDOM, $imageNode);
                }

                return $items;
            },
            'isCarousel' => true,
            'moreUrl' => function () use ($node, $googleDOM) {
                $a = $googleDOM->cssQuery('g-tray-header ._Nbi a');
                $a = $a->item(0);
                if ($a instanceof \DOMElement) {
                    return $googleDOM->getUrl()->resolveAsString($a->getAttribute('href'));
                }
                return null;
            }
        ];

//        $imageNodes = $googleDOM->cssQuery('.rg_ul>div._ZGc a', $node);
//        foreach ($imageNodes as $imgNode) {
//            $item['images'][] = $this->parseItem($googleDOM, $imgNode);
//        }
        $resultSet->addItem(new BaseResult(NaturalResultType::IMAGE_GROUP, $item));
    }
    /**
     * @param GoogleDOM $googleDOM
     * @param \DOMElement $imgNode
     * @return array
     */
    private function parseItem(GoogleDOM $googleDOM, \DOMElement $imgNode)
    {
        $data =  [
            'sourceUrl' => function () use ($imgNode, $googleDOM) {
                // TODO parse json content in .rg_meta
            },
            'targetUrl' => function () use ($imgNode, $googleDOM) {
                // TODO parse json content in .rg_meta
            },
            'image' => function () use ($imgNode, $googleDOM) {
                // TODO parse json content in .rg_meta
            },
        ];

        return new BaseResult(NaturalResultType::IMAGE_GROUP_IMAGE, $data);
    }
}

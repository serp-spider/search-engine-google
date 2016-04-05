<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\Core\UrlArchive;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterace;
use Serps\SearchEngine\Google\NaturalResultType;

class ImageGroup implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterace
{

    public function match(GoogleDom $dom, \DOMElement $node)
    {
        if ($node->hasAttribute('id') && $node->getAttribute('id') == 'imagebox_bigimages') {
            return self::RULE_MATCH_MATCHED;
        } else {
            return self::RULE_MATCH_NOMATCH;
        }
    }
    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet)
    {
        $item = [
            'images' => [],
            'moreUrl' => function () use ($node, $googleDOM) {
                $aTag = $googleDOM->getXpath()->query('descendant::div[@class="_Icb _kk _wI"]/a', $node)->item(0);
                if (!$aTag) {
                    return $googleDOM->getUrl()->resolve('/');
                }
                return $googleDOM->getUrl()->resolve($aTag->getAttribute('href'));

            }
        ];

        $xpathCards = "descendant::ul[@class='rg_ul']/div[@class='_ZGc bili uh_r rg_el ivg-i']//a";
        $imageNodes = $googleDOM->getXpath()->query($xpathCards, $node);
        foreach ($imageNodes as $imgNode) {
            $item['images'][] = $this->parseItem($googleDOM, $imgNode);
        }
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
                $img = $googleDOM->getXpath()->query('descendant::img', $imgNode)->item(0);
                if (!$img) {
                    return $googleDOM->getUrl()->resolve('/');
                }
                return $googleDOM->getUrl()->resolve($img->getAttribute('title'));
            },
            'targetUrl' => function () use ($imgNode, $googleDOM) {
                return $googleDOM->getUrl()->resolve($imgNode->getAttribute('href'));
            },
            'image' => function () use ($imgNode, $googleDOM) {
                $img = $googleDOM->getXpath()->query('descendant::img', $imgNode)->item(0);
                if (!$img) {
                    return '';
                }
                return $img->getAttribute('src');
            },
        ];
        
        return new BaseResult(NaturalResultType::IMAGE_GROUP_IMAGE, $data);
    }
}

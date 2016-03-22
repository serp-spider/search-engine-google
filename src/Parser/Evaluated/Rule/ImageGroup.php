<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\ResultSet;
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
    public function parse(GoogleDom $googleDOM, \DomElement $group, ResultSet $resultSet)
    {
        $item = [
            'images' => []
        ];

        $xpathCards = "descendant::ul[@class='rg_ul']/div[@class='_ZGc bili uh_r rg_el ivg-i']//a";
        $imageNodes = $googleDOM->getXpath()->query($xpathCards, $group);
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
    protected function parseItem(GoogleDOM $googleDOM, \DOMElement $imgNode)
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

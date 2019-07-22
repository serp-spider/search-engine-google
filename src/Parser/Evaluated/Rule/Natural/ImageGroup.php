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

class ImageGroup implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
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
            'isCarousel' => false,
            'moreUrl' => function () use ($node, $googleDOM) {
                $aTag = $googleDOM->getXpath()->query('descendant::div[@class="_Icb _kk _wI"]/a', $node)->item(0);
                if (!$aTag) {
                    return $googleDOM->getUrl()->resolve('/');
                }
                return $googleDOM->getUrl()->resolveAsString($aTag->getAttribute('href'));
            }
        ];

        // TODO: detect no image (google dom update)
        $imageNodes = $googleDOM->cssQuery('.rg_ul>div._ZGc a', $node);
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
    private function parseItem(GoogleDom $googleDOM, \DOMElement $imgNode)
    {
        $data =  [
            'sourceUrl' => function () use ($imgNode, $googleDOM) {
                $img = $googleDOM->getXpath()->query('descendant::img', $imgNode)->item(0);
                if (!$img) {
                    return $googleDOM->getUrl()->resolve('/');
                }
                return $googleDOM->getUrl()->resolveAsString($img->getAttribute('title'));
            },
            'targetUrl' => function () use ($imgNode, $googleDOM) {
                return $googleDOM->getUrl()->resolveAsString($imgNode->getAttribute('href'));
            },
            'image' => function () use ($imgNode, $googleDOM) {
                $img = $googleDOM->getXpath()->query('descendant::img', $imgNode)->item(0);
                if (!$img) {
                    return '';
                }
                return MediaFactory::createMediaFromSrc($img->getAttribute('src'));
            },
        ];

        return new BaseResult(NaturalResultType::IMAGE_GROUP_IMAGE, $data);
    }
}

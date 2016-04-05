<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Raw\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\GoogleUrl;
use Serps\SearchEngine\Google\GoogleUrlArchive;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterace;
use Serps\SearchEngine\Google\NaturalResultType;

class ImageGroup implements ParsingRuleInterace
{
    public function match(GoogleDom $dom, \DOMElement $node)
    {
        if ($node->getAttribute('class') !== 'g') {
            return ParsingRuleInterace::RULE_MATCH_NOMATCH;
        }

        /* @var $aTag \DOMElement */
        $aTag=$dom->getXpath()
            ->query("descendant::h3[@class='r'][1]/a", $node)
            ->item(0);

        if ($aTag) {
            $url = $aTag->getAttribute('href');

            if (strpos($url, '/search') == 0) {
                // todo URLArchive::copy
                $url = GoogleUrlArchive::fromString($dom->getUrl()->resolve($url));

                if ($url->getResultType() == GoogleUrl::RESULT_TYPE_IMAGES) {
                    return  ParsingRuleInterace::RULE_MATCH_MATCHED;
                }
            }
        }


        return ParsingRuleInterace::RULE_MATCH_NOMATCH;

    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet)
    {
        $xpath = $googleDOM->getXpath();
        $aTag=$xpath
            ->query("descendant::h3[@class='r'][1]/a", $node)
            ->item(0);

        $data = [
            'moreUrl' => $googleDOM->getUrl()->resolve($aTag->getAttribute('href')),
            'images'  => []
        ];

        $imageNodes = $xpath->query('div/a', $node);

        foreach ($imageNodes as $imgNode) {
            $data['images'][] = $this->parseItem($googleDOM, $imgNode);
        }

        $resultSet->addItem(new BaseResult(NaturalResultType::IMAGE_GROUP, $data));
    }

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

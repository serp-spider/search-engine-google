<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Rule;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\ResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;

class ImageGroup implements ParsingRuleInterace
{

    public function match(\DOMElement $node)
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
        $resultSet->addItem(new BaseResult('imageGroup', $item));
    }
    /**
     * @param GoogleDOM $googleDOM
     * @param \DOMElement $imgNode
     * @return array
     */
    protected function parseItem(GoogleDOM $googleDOM, \DOMElement $imgNode)
    {
        $data = [
            'targetUrl' => $imgNode->getAttribute('href')
        ];

        $img = $imgNode->firstChild;
        if ($img) {
            $data['pageUrl'] = $img->getAttribute('title');
        }

        return $data;
    }
}

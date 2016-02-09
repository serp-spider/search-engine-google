<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Rule;

use Serps\Core\Serp\ResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;

class InDepthArticle implements ParsingRuleInterace
{

    public function match(\DOMElement $node)
    {
        if ($node->getAttribute('class') == 'r-search-3') {
            return self::RULE_MATCH_MATCHED;
        }
        return self::RULE_MATCH_NOMATCH;
    }
    public function parse(GoogleDom $googleDOM, \DomElement $group, ResultSet $resultSet)
    {

        $item = [
            'cards' => []
        ];

        $xpathCards = "li[contains(concat(' ',normalize-space(@class),' '),' card-section ')]";
        $cardNodes = $googleDOM->getXpath()->query($xpathCards, $group);

        foreach ($cardNodes as $cardNode) {
            $item['cards'][] = $this->parseItem($googleDOM, $cardNode);
        }
    }
    /**
     * @param GoogleDOM $googleDOM
     * @param \DomElement $node
     * @return array
     */
    protected function parseItem(GoogleDOM $googleDOM, \DomElement $node)
    {
        $xpathTitle = "descendant::h3[@class = 'r']/a";
        $aTag = $googleDOM->getXpath()->query($xpathTitle, $node)->item(0);
        $title = $aTag->nodeValue;
        $targetUrl = $aTag->getAttribute('href');

        return [
            'title' => $title,
            'targetUrl' => $targetUrl
        ];
    }
}

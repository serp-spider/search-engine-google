<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Rule;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\ResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Test\TDD\SearchEngine\Google\GoogleDomTest;

class InTheNews implements ParsingRuleInterace
{

    public function match(\DOMElement $node)
    {
        $child = $node->firstChild;
        if (!$child || !($child instanceof \DOMElement)) {
            return self::RULE_MATCH_NOMATCH;
        }
        if ($child->getAttribute('class') == 'mnr-c _yE') {
            return self::RULE_MATCH_MATCHED;
        }
        return self::RULE_MATCH_NOMATCH;
    }


    public function parse(GoogleDom $googleDOM, \DomElement $group, ResultSet $resultSet)
    {
        $item = [
            'cards' => []
        ];
        $xpathCards = "div/div[contains(concat(' ',normalize-space(@class),' '),' card-section ')]";
        $cardNodes = $googleDOM->getXpath()->query($xpathCards, $group);

        foreach ($cardNodes as $cardNode) {
            $item['cards'][] = $this->parseItem($googleDOM, $cardNode);
        }

        $resultSet->addItem(new BaseResult('inTheNews', $item));
    }
    /**
     * @param GoogleDOM $googleDOM
     * @param \DomElement $node
     * @return array
     */
    protected function parseItem(GoogleDOM $googleDOM, \DomElement $node)
    {
        $card = [];
        $xpathTitle = "descendant::a[@class = '_Dk']";
        $aTag = $googleDOM->getXpath()->query($xpathTitle, $node)->item(0);
        if ($aTag) {
            $card['title'] = $aTag->nodeValue;
            $card['targetUrl'] = $aTag->getAttribute('href');
        }
        return $card;
    }
}

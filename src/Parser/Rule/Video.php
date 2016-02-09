<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Rule;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\ResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;

class Video implements ParsingRuleInterace
{

    public function match(\DOMElement $node)
    {
        if ($node->getAttribute('class') == 'g mnr-c g-blk') {
            return self::RULE_MATCH_MATCHED;
        } else {
            return self::RULE_MATCH_NOMATCH;
        }
    }
    public function parse(GoogleDom $googleDOM, \DomElement $itemDom, ResultSet $resultSet)
    {
        $xpath = $googleDOM->getXpath();
        $aTag=$xpath->query("descendant::h3[@class='r'][1]/a", $itemDom)->item(0);

        $resultSet->addItem(new BaseResult('video', [
            'targetUrl' => $aTag->getAttribute('href'),
            'title' => $aTag->nodeValue,
        ]));
    }
}

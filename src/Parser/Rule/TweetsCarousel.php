<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Rule;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\ResultSet;

class TweetsCarousel implements ParsingRuleInterace
{

    public function match(\DOMElement $node)
    {
        if ($node->getAttribute('class') == 'g') {
            foreach ($node->childNodes as $node) {
                if ($node instanceof \DOMElement && $node->getAttribute('class') == '_Zfh') {
                    return self::RULE_MATCH_MATCHED;
                }
            }
        }
        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $dom, \DomElement $node, ResultSet $resultSet)
    {
        $xpath = $dom->getXpath();

        /* @var $aTag \DOMElement */
        $aTag=$xpath
            ->query("descendant::h3[@class='r'][1]//a", $node)
            ->item(0);

        if ($aTag) {
            $title = $aTag->nodeValue;

            $user = preg_match('/@([A-Za-z0-9_]{1,15})/', $title, $match);

            $data = [
                'snippet' => $node->C14N(),
                'title'   => $title,
                'url'     => $aTag->getAttribute('href'),
                'user'    => $user
            ];

            $item = new BaseResult('tweetsCarousel', $data);
            $resultSet->addItem($item);
        }
    }
}

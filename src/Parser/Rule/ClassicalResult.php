<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Rule;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\ResultSet;

class ClassicalResult implements ParsingRuleInterace
{

    public function match(\DOMElement $node)
    {
        if ($node->getAttribute('class') == 'g') {
            foreach ($node->childNodes as $node) {
                if ($node instanceof \DOMElement && $node->getAttribute('class') == 'rc') {
                    return self::RULE_MATCH_MATCHED;
                }
            }
        }
        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $dom, \DomElement $node, ResultSet $resultSet)
    {
        $xpath = $dom->getXpath();

        // find the tilte/url
        /* @var $aTag \DOMElement */
        $aTag=$xpath
            ->query("descendant::h3[@class='r'][1]/a", $node)
            ->item(0);
        if (!$aTag) {
            return;
        }

        $data = [
            'snippet' => $node->C14N(),
            'title'   => $aTag->nodeValue,
            'url'     => $aTag->getAttribute('href'),
        ];

        $item = new BaseResult('classical', $data);
        $resultSet->addItem($item);
    }
}

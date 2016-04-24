<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterace;
use Serps\SearchEngine\Google\NaturalResultType;

class ClassicalWithLargeVideo implements ParsingRuleInterace
{

    public function match(GoogleDom $dom, \DOMElement $node)
    {
        if ($node->getAttribute('class') == 'g mnr-c g-blk') {
            return self::RULE_MATCH_MATCHED;
        } else {
            return self::RULE_MATCH_NOMATCH;
        }
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {
        $xpath = $dom->getXpath();
        $aTag = $xpath->query("descendant::h3[@class='r'][1]/a", $node)->item(0);

        if (!$aTag) {
            return false;
        }

        $destinationTag = $xpath
            ->query("descendant::div[@class='f kv _SWb']/cite", $node)
            ->item(0);

        $data = [
            'title'   => $aTag->nodeValue,
            'url'     => $dom->getUrl()->resolve($aTag->getAttribute('href'), 'string'),
            'destination' => $destinationTag ? $destinationTag->nodeValue : null,
            'description' => null,
            'videoLarge'  => true,
            'videoCover'  => function () use ($node, $xpath) {
                $imageTag = $xpath
                    ->query("descendant::div[@class='_ELb']/img", $node)
                    ->item(0);
                if ($imageTag) {
                    return $imageTag->getAttribute('src');
                } else {
                    return null;
                }
            }
        ];

        $resultSet->addItem(new BaseResult(NaturalResultType::CLASSICAL_VIDEO, $data));
    }
}

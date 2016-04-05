<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterace;
use Serps\SearchEngine\Google\NaturalResultType;

class ClassicalResult implements ParsingRuleInterace
{

    public function match(GoogleDom $dom, \DOMElement $node)
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

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
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

        $destinationTag = $xpath
            ->query("descendant::div[@class='f kv _SWb']/cite", $node)
            ->item(0);

        $descriptionTag = $xpath
            ->query("descendant::span[@class='st']", $node)
            ->item(0);

        // classical result can have a video thumbnail
        $videoThumb = $xpath
            ->query("descendant::g-img[@class='_ygd']/img", $node)
            ->item(0);


        $data = [
            'title'   => $aTag->nodeValue,
            'url'     => $dom->getUrl()->resolve($aTag->getAttribute('href')),
            'destination' => $destinationTag ? $destinationTag->nodeValue : null,
            'description' => $descriptionTag ? $descriptionTag->nodeValue : null
        ];

        $resultTypes = [NaturalResultType::CLASSICAL];

        if ($videoThumb) {
            array_unshift($resultTypes, NaturalResultType::CLASSICAL_VIDEO);
            $data['videoLarge'] = false;

            $data['videoCover'] = function () use ($videoThumb) {
                if ($videoThumb) {
                    return $videoThumb->getAttribute('src');
                } else {
                    return null;
                }
            };
        }

        $item = new BaseResult($resultTypes, $data);
        $resultSet->addItem($item);
    }
}

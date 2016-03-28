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

class ClassicalLargeVideo implements ParsingRuleInterace
{
    public function match(GoogleDom $dom, \DOMElement $node)
    {
        if ($node->childNodes->length > 0 && $node->childNodes->item(0)->getAttribute('class') == '_uXc hp-xpdbox') {
            return ParsingRuleInterace::RULE_MATCH_MATCHED;
        }

        return ParsingRuleInterace::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {
        $xpath = $dom->getXpath();

        // find the tilte/url
        /* @var $aTag \DOMElement */
        $aTag = $xpath
            ->query("descendant::h3[@class='_X8d']/a", $node)
            ->item(0);

        $url=$aTag->getAttribute('href');
        $url = $dom->getEffectiveUrl()->resolve($url)->getParamRawValue('q');

        $destinationTag = $xpath
            ->query("descendant::div[@class='_Y8d']/cite", $node)
            ->item(0);

        $data = [
            'title'   => $aTag->nodeValue,
            'url'     => GoogleUrlArchive::fromString($url),
            'destination' => $destinationTag ? $destinationTag->nodeValue : null,
            'description' => null,
            'videoLarge'  => true,
            'videoCover'  => function () use ($node, $xpath) {
                $imageTag = $xpath
                    ->query('descendant::a/img', $node)
                    ->item(0);

                if ($imageTag) {
                    return $imageTag->getAttribute('src');
                } else {
                    return null;
                }
            }
        ];

        $resultSet->addItem(
            new BaseResult([NaturalResultType::CLASSICAL_VIDEO, NaturalResultType::CLASSICAL], $data)
        );
    }
}

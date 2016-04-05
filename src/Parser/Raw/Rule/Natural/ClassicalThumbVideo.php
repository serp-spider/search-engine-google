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

class ClassicalThumbVideo implements ParsingRuleInterace
{
    public function match(GoogleDom $dom, \DOMElement $node)
    {
        if ($node->childNodes->length > 0 && $node->childNodes->item(0)->getAttribute('class') == 'ts') {
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
            ->query("descendant::h3[@class='r']/a", $node)
            ->item(0);

        $url=$aTag->getAttribute('href');
        $url = $dom->getUrl()->resolve($url)->getParamRawValue('q');

        $destinationTag = $xpath
            ->query("descendant::td/cite[@class='kv']", $node)
            ->item(0);


        $descriptionTag = $xpath
            ->query("descendant::span[@class='st']", $node)
            ->item(0);


        $data = [
            'title'   => $aTag->nodeValue,
            'url'     => GoogleUrlArchive::fromString($url),
            'destination' => $destinationTag ? $destinationTag->nodeValue : null,
            'description' => $descriptionTag ? $descriptionTag->nodeValue : null,
            'videoLarge'  => false,
            'videoCover'  => function () use ($node, $xpath) {
                $imageTag = $xpath
                    ->query("descendant::div[@class='th']//img", $node)
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

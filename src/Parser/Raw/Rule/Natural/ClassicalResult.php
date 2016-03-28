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

class ClassicalResult implements ParsingRuleInterace
{
    public function match(GoogleDom $dom, \DOMElement $node)
    {
        if ($node->getAttribute('class') !== 'g') {
            return ParsingRuleInterace::RULE_MATCH_NOMATCH;
        }

        /* @var $aTag \DOMElement */
        $aTag=$dom->getXpath()
            ->query("descendant::h3[@class='r'][1]/a", $node)
            ->item(0);

        if (!$aTag) {
            return ParsingRuleInterace::RULE_MATCH_NOMATCH;
        }

        $url = $aTag->getAttribute('href');

        if (strpos($url, '/url') !== 0) {
            return ParsingRuleInterace::RULE_MATCH_NOMATCH;
        }

        return ParsingRuleInterace::RULE_MATCH_MATCHED;
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {
        $xpath = $dom->getXpath();

        // find the tilte/url
        /* @var $aTag \DOMElement */
        $aTag=$xpath
            ->query("descendant::h3[@class='r'][1]/a", $node)
            ->item(0);

        $destinationTag = $xpath
            ->query("descendant::div[@class='kv']/cite", $node)
            ->item(0);

        $descriptionTag = $xpath
            ->query("descendant::span[@class='st']", $node)
            ->item(0);

        $url=$aTag->getAttribute('href');

        $resultSet->addItem(
            new BaseResult(NaturalResultType::CLASSICAL, [
                'title'   => $aTag->nodeValue,
                'url'     => $url,
                'destination' => $destinationTag ? $destinationTag->nodeValue : null,
                'description' => $descriptionTag ? $descriptionTag->nodeValue : null
            ])
        );
    }
}

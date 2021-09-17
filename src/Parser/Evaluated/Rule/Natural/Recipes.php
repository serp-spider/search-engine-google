<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Media\MediaFactory;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\Core\UrlArchive;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Google\NaturalResultType;

class Recipes implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->parentNode->parentNode->getAttribute('jsname') == 'gI9xcc'
        ) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }


    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet)
    {
        $urls = $googleDOM->getXpath()->query('descendant::g-link', $node->childNodes->item(1));
        $item = [];

        if($urls->length> 0) {
            foreach ($urls as $urlNode) {
                $item['recipes_links'][] = ['link' => $urlNode->firstChild->getAttribute('href')];
            }
        }

        $resultSet->addItem(new BaseResult(NaturalResultType::IMAGE_GROUP, $item));
    }
}

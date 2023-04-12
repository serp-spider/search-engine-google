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

class HighlyLocalized implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {

        if (
            str_contains($node->getAttribute('class'),  'vqkKIe') &&
            str_contains($node->getAttribute('class'),  'wHYlTd')
        ) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }


    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {

        $highlyLocalizedNode = $googleDOM->getXpath()->query("descendant::*[contains(concat(' ', normalize-space(@class), ' '), ' BBwThe ')]", $node);

        if ($highlyLocalizedNode->length > 0) {
            //$highlyLocalizedNode->item(0)->textContent - location name
            $resultSet->addItem(new BaseResult(NaturalResultType::HIGHLY_LOCALIZED, [true]));
        }

    }
}

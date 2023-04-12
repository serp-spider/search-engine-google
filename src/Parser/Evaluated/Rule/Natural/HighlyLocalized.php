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
        $highlyLocalizedNode = $dom->getXpath()->query("descendant::update-location", $node);

        if ($highlyLocalizedNode->length > 0) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }


    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {

        $highlyLocalizedNode = $googleDOM->getXpath()->query("ancestor::*[contains(concat(' ', normalize-space(@id), ' '), ' oFNiHe ')]", $node);

        if ($highlyLocalizedNode->length > 0) {
            $resultSet->addItem(new BaseResult(NaturalResultType::HIGHLY_LOCALIZED, [true]));
        }

    }
}

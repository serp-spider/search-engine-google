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

class Misspelling implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if (
            $node->getAttribute('id') =='oFNiHe'
        ) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }


    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {

        $mispellingNode = $googleDOM->getXpath()->query("descendant::*[contains(concat(' ', normalize-space(@class), ' '), ' card-section KDCVqf ')]", $node);

        if ($mispellingNode->length > 0) {
            $resultSet->addItem(new BaseResult(NaturalResultType::MISSPELLING, [
                $googleDOM->getXpath()->query("descendant::a", $mispellingNode->item(0))->item(0)->textContent
            ]));
        }

    }
}

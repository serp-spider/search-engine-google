<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\NaturalResultType;

class VisualDigest implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{
    protected $hasSerpFeaturePosition = false;
    protected $hasSideSerpFeaturePosition = false;

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if (strpos($node->getAttribute('class'), 'e8Ck0d') !== false) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $visualDigestItems = $googleDOM->getXpath()->query('descendant::*[contains( @data-attrid,"VisualDigest" )]   ', $node);
        $item = [];

        if ($visualDigestItems->length > 1) {
            foreach ($visualDigestItems as $visualDigestItem) {
                $visualDigestType = $visualDigestItem->getAttribute('data-attrid');
                $link = $googleDOM->getXpath()->query('descendant::a', $visualDigestItem);
                $info = true;
                if (!empty($link->item(0))) {
                    $info = $link->item(0)->getAttribute('href');
                }
                $item[] = [$visualDigestType => $info];
            }

            $resultSet->addItem(new BaseResult(NaturalResultType::VISUAL_DIGEST , $item, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
        }
    }
}

<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Media\MediaFactory;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;

/**
 * This rule extracts video groups as present on mobile results
 */
class Videos implements ParsingRuleInterface
{

    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = false;

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->hasClass('e4xoPb')) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {

        $aHrefs = $googleDOM->getXpath()->query('descendant::a[@class="X5OiLe"]', $node);

        if ($aHrefs->length == 0) {
            return;
        }

        $items = [];

        foreach ($aHrefs as $url) {
            $items[] = [
                'url'    => $url->getAttribute('href'),
                'height' => '',
            ];
        }

        $resultSet->addItem(new BaseResult(NaturalResultType::VIDEOS, $items, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
    }
}

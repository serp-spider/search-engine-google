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

class HotelsMobile implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{

    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = false;

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if (str_contains($node->getAttribute('class'),  'hNKF2b')
        ) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }


    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $hotels = $googleDOM->getXpath()->query("descendant::*[contains(concat(' ', normalize-space(@class), ' '), ' BTPx6e')]", $node);
        $item = [];

        if($hotels->length> 0) {
            foreach ($hotels as $urlNode) {
                try {
                    $item['hotels_names'][] = ['name' => $urlNode->nodeValue];
                } catch (\Exception $e) {

                }
            }
            $resultSet->addItem(new BaseResult(NaturalResultType::HOTELS_MOBILE, $item, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));
        }


    }
}

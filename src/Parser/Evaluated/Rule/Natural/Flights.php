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

class Flights implements \Serps\SearchEngine\Google\Parser\ParsingRuleInterface
{

    protected $hasSerpFeaturePosition = true;
    protected $hasSideSerpFeaturePosition = false;

    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        $class = $node->getAttribute('class');
        if (!empty($class) && strpos($class, 'LQQ1Bd') !== false && $node->getChildren()->count() != 0) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }


    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        if ($googleDOM->xpathQuery("ancestor::g-accordion-expander", $node)->length >0) {
            return false;
        }

        //bCOlv - this is a kowledge used in things to know/people also ask. these are not flights results
        if (
            $googleDOM->xpathQuery("ancestor::div[contains(concat(' ', normalize-space(@class), ' '), ' bCOlv ')]", $node)->length > 0
        ) {
            return false;
        }

        $urls = $googleDOM->getXpath()->query('descendant::a', $node->firstChild);
        $item = [];

        if($urls->length> 0) {
            foreach ($urls as $urlNode) {
                $item['flights_names'][] = ['name' => $urlNode->firstChild->textContent, 'url' => $urlNode->getAttribute('href')];
            }
        }

        $resultSet->addItem(new BaseResult(NaturalResultType::FLIGHTS, $item, $node, $this->hasSerpFeaturePosition, $this->hasSideSerpFeaturePosition));    }
}

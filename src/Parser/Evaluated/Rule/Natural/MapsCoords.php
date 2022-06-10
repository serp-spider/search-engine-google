<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Dom\DomXpath;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Google\NaturalResultType;

class MapsCoords implements ParsingRuleInterface
{
    public function match(GoogleDom $dom, \Serps\Core\Dom\DomElement $node)
    {
        if ($node->getAttribute('class') == 'H93uF' || $node->getAttribute('class') == 'o8ebK') {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $googleDOM, \DomElement $node, IndexedResultSet $resultSet, $isMobile=false)
    {

        $aNode = $node->getElementsByTagName('a')->item(0);
        $href = $aNode->getAttribute('href');
        preg_match('/rllag=([^,]*),([^,]*)/', $href, $coords);

        if(!empty($coords['1']) && !empty($coords['2'])) {
            $item['lat'] = $coords['1']/pow(10, strlen($coords['1'])-2);
            $item['long'] = $coords['2']/pow(10, strlen($coords['2'])-2);;
            $resultSet->addItem(new BaseResult(NaturalResultType::MAPS_COORDONATES , $item));
        }
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: aurimasgladutis
 * Date: 9/1/16
 * Time: 4:25 PM
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\WikiBox;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Css;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterace;

class Header implements ParsingRuleInterace
{
    /**
     * @param GoogleDom   $dom
     * @param \DOMElement $node
     *
     * @return int
     */
    public function match(GoogleDom $dom, \DOMElement $node)
    {
        return $node->getAttribute('class') == 'kp-header' ? self::RULE_MATCH_MATCHED : self::RULE_MATCH_NOMATCH;
    }

    /**
     * @param GoogleDom        $dom
     * @param \DOMElement      $node
     * @param IndexedResultSet $resultSet
     */
    public function parse(GoogleDom $dom, \DOMElement $node, IndexedResultSet $resultSet)
    {
        $xpath = $dom->getXpath();

        /* @var $title \DOMElement */
        $title = $xpath
            ->query(Css::toXPath('.kp-hc .kno-ecr-pt.kno-fb-ctx'), $node)
            ->item(0);

        $description = $xpath
            ->query(Css::toXPath('.kp-hc ._gdf._LAf span'), $node)
            ->item(0);

        $map = $xpath
            ->query(Css::toXPath('#media_result_group ._tN._eXg a'), $node) //  .kno-fb-ctx .rhsg4.rhsmap5col a img
            ->item(0);

        $mapData = null;
        if (null !== $map) {
            $url = $map->getAttribute('href');
            $placeName = null;
            $placeData = null;
            if (preg_match('/maps\/place\/(.*?)\/data=(.*?)\?/', $url, $matches)) {
                $placeName = $matches[1];
                $placeData = $matches[2];
            }
            $mapData = [
                'url' => $url,
                'placeName' => $placeName,
                'placeData' => $placeData,
            ];
        }

        $data = [
            'title'   => $title ? $title->nodeValue : null,
            'description' => $description ? $description->nodeValue : null,
            'map' => $mapData,
        ];

        $item = new BaseResult('header', $data);
        $resultSet->addItem($item);
    }
}

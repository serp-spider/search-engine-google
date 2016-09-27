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

class Description implements ParsingRuleInterace
{
    /**
     * @param GoogleDom   $dom
     * @param \DOMElement $node
     *
     * @return int
     */
    public function match(GoogleDom $dom, \DOMElement $node)
    {
        return $node->getAttribute('class') == '_G1d _wle _xle' ? self::RULE_MATCH_MATCHED : self::RULE_MATCH_NOMATCH;
    }

    /**
     * @param GoogleDom        $dom
     * @param \DOMElement      $node
     * @param IndexedResultSet $resultSet
     */
    public function parse(GoogleDom $dom, \DOMElement $node, IndexedResultSet $resultSet)
    {
        $xpath = $dom->getXpath();

        /* @var $description \DOMElement */
        $description = $xpath
            ->query(Css::toXPath('.kno-rdesc span:first-child'), $node)
            ->item(0);

        $wikiLink = $xpath
            ->query(Css::toXPath('.kno-rdesc span:last-child a'), $node)
            ->item(0);

        $data = [
            'description'   => $description ? $description->nodeValue : null,
            'wikipedia_link' => $wikiLink ? $wikiLink->getAttribute('href') : null,
        ];

        $item = new BaseResult('description', $data);
        $resultSet->addItem($item);
    }
}

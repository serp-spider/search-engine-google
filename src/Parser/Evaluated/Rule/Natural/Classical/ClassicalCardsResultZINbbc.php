<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical;

use Serps\Core\Dom\DomElement;
use Serps\Core\Dom\DomNodeInterface;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;

/**
 * First seen in 2018 in mobile pages
 */
class ClassicalCardsResultZINbbc implements ParsingRuleInterface
{

    public function match(GoogleDom $dom, DomElement $node)
    {
        $res = $dom->cssQuery('.ZINbbc.xpd a.C8nzq', $node);

        if ($res->length == 1) {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {
        $classicalData = $this->parseNode($dom, $node);

        $resultTypes = [NaturalResultType::CLASSICAL];

        $item = new BaseResult($resultTypes, $classicalData);
        $resultSet->addItem($item);
    }

    protected function parseNode(GoogleDom $dom, DomNodeInterface $node)
    {
        return [
            'title' => function () use ($dom, $node) {
                return $dom
                    ->cssQuery('a .pIpgAc', $node)
                    ->getNodeAt(0)
                    ->getNodeValue();
            },
            'isAmp' => function () use ($dom, $node) {
                return $dom
                    ->cssQuery('.ZseVEf', $node)
                    ->length > 0;
            },
            'url' => function () use ($dom, $node) {
                return $dom
                    ->cssQuery('a.C8nzq', $node)
                    ->getNodeAt(0)
                    ->getAttribute('href');
            },
            'destination' => function () use ($dom, $node) {
                return $dom
                    ->cssQuery('span.QHTnWc', $node)
                    ->getNodeAt(0)
                    ->nodeValue;
            },
            'description' => function () use ($dom, $node) {
                $res = $dom
                    ->cssQuery('.JTuIPc', $node);

                if ($res->length > 1) {
                    return $dom->cssQuery('.pIpgAc', $res->getNodeAt(1))->getNodeAt(0)->getNodeValue();
                } else {
                    return null;
                }
            }
        ];
    }
}

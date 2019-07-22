<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical;

use Serps\Core\Dom\DomElement;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;

/**
 * Special version of classical card that appeared end 2017 in mobile serps.
 * Those results are identified by the class ._Z1m
 *
 * Z1m card results have nothing in common with other results so they have to be parsed independently
 */
class ClassicalCardsResultZ1m implements ParsingRuleInterface
{

    public function match(GoogleDom $dom, DomElement $node)
    {
        if ($node->childNodes->length == 1) {
            $childNode = $node->getChildren()->getNodeAt(0);

            // TODO _Z1m results appear to be outdated
            // check if has class _Z1m
            if ($childNode->hasClass('_Z1m')) {
                // check _a5r
                if ($node->childNodes->item(0)->childNodes->length == 1) {
                    /** @var DomElement $subChildNode */
                    $subChildNode = $childNode->childNodes->item(0);

                    if ($subChildNode->hasClass('_a5r')) {
                        return self::RULE_MATCH_MATCHED;
                    }
                }

                // check a._Olt._bCp
                if ($dom->cssQuery('a._Olt._bCp', $node)->length > 0) {
                    return self::RULE_MATCH_MATCHED;
                }
            }
        }

        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {
        $data = $this->parseNode($dom, $node->childNodes->item(0));

        $resultTypes = [NaturalResultType::CLASSICAL];

        $item = new BaseResult($resultTypes, $data);
        $resultSet->addItem($item);
    }

    protected function parseNode(GoogleDom $dom, DomElement $node)
    {
        return [
            'title' => function () use ($dom, $node) {
                return $dom
                    ->cssQuery('._ees', $node)
                    ->item(0)
                    ->nodeValue;
            },
            'isAmp' => function () use ($dom, $node) {
                return $dom
                    ->cssQuery('.amp_r', $node)
                    ->length > 0;
            },
            'url' => function () use ($dom, $node) {
                return $dom
                    ->cssQuery('a._Olt', $node)
                    ->item(0)
                    ->getAttribute('href');
            },
            'destination' => function () use ($dom, $node) {
                return $dom
                    ->cssQuery('span._Clt', $node)
                    ->item(0)
                    ->nodeValue;
            },
            'description' => function () use ($dom, $node) {
                $res = $dom
                    ->cssQuery('div>div._bCp>div._H1m', $node);

                if ($res->length > 0) {
                    return $res->item(0)->getNodeValue();
                } else {
                    return null;
                }
            }
        ];
    }
}

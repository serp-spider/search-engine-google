<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\Core\Dom\DomElement;
use Serps\Core\Dom\DomNodeList;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;

/**
 * Class PeopleAlsoAsk
 * @package Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural
 *
 * Note: "people also ask" would results would also match against "Knowledge" parser.
 *       For this reason "paa" parser must be processed before knowledge parser.
 *
 */
class PeopleAlsoAsk implements ParsingRuleInterface
{

    public function match(GoogleDom $dom, DomElement $node)
    {
        if ($node->hasClasses(['kno-kp', 'mnr-c'])) {
            $childNodes = new DomNodeList($node->childNodes, $dom);

            if ($childNodes->hasClass('_thf')) {
                return self::RULE_MATCH_MATCHED;
            }
        }
        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {

        $data = [
            'questions' => function () use ($dom, $node) {
                $items = [];
                $nodes = $dom->cssQuery('._sgo>._qgo', $node);
                foreach ($nodes as $questionNode) {
                    $items[] = new BaseResult(NaturalResultType::PAA_QUESTION, [
                        'question' => function () use ($questionNode, $dom) {
                            return $dom->cssQuery('._rhf', $questionNode)
                                ->getNodeAt(0)
                                ->getNodeValue();
                        }
                    ]);
                }

                return $items;
            }
        ];

        $resultSet->addItem($a = new BaseResult(NaturalResultType::PEOPLE_ALSO_ASK, $data));
    }
}

<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;

class LargeClassicalResult extends ClassicalResult
{

    public function match(GoogleDom $dom, \DOMElement $node)
    {
        if ($node->getAttribute('class') == 'g') {
            if ($node->childNodes->length == 1) {
                $child = $node->childNodes->item(0);
                foreach ($child->childNodes as $cchild) {
                    if ($cchild instanceof \DOMElement && $cchild->getAttribute('class') == 'rc') {
                        return self::RULE_MATCH_MATCHED;
                    }
                }
            }
        }
        return self::RULE_MATCH_NOMATCH;
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {
        $data = $this->parseNode($dom, $node);
        
        $resultTypes = [NaturalResultType::CLASSICAL, NaturalResultType::CLASSICAL_LARGE];

        $item = new BaseResult($resultTypes, $data);
        $resultSet->addItem($item);
    }
}

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

class ClassicalCardsResult extends ClassicalResult
{

    public function match(GoogleDom $dom, DomElement $node)
    {
        if ($node->hasClass('mnr-c')) {
            $hasgblk = $node->hasClass('g-blk');
            // class g-blk is common to classical large, answer box, and some other cards results,
            // but not present on base classical results
            // class ._Hi is unique to large classical results

            if ((!$hasgblk || ($hasgblk && $dom->cssQuery('._Hi', $node)->length == 1))
                && $dom->cssQuery('.rc', $node)->length == 1
            ) {
                return self::RULE_MATCH_MATCHED;
            }
        }
        return self::RULE_MATCH_NOMATCH;
    }


    /**
     * Is large dectection is not the same for cards and non cards classical results
     * @param GoogleDom $dom
     * @param \DomElement $node
     * @return bool
     */
    protected function isLarge(GoogleDom $dom, \DomElement $node)
    {
        return $dom->cssQuery('._Hi', $node)->length == 1;
    }
}

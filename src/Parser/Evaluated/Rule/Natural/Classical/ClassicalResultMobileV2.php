<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical;

use Serps\Core\Dom\DomElement;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;

class ClassicalResultMobileV2 extends ClassicalResultMobile
{
    protected $resultType = NaturalResultType::CLASSICAL_MOBILE;

    public function match(GoogleDom $dom, DomElement $node)
    {
        if ($node->getAttribute('id') == 'center_col') {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }
}

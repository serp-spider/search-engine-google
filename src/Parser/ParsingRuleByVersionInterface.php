<?php

namespace Serps\SearchEngine\Google\Parser;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\OrganicResultObject;

interface ParsingRuleByVersionInterface
{
    public function parseNode(GoogleDom $dom, \DomElement $organicResult, OrganicResultObject $organicResultObject);
}

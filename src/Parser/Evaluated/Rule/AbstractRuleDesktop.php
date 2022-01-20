<?php
namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule;

use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalResultEngine;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\Versions\Desktop\DesktopV1;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\Versions\Desktop\DesktopV2;

class AbstractRuleDesktop extends ClassicalResultEngine
{
    protected $rulesForParsing;

    protected function generateRules()
    {
        return [
            new DesktopV1(),
            new DesktopV2(),
        ];
    }

    public function getRules()
    {
        if (null == $this->rulesForParsing) {
            $this->rulesForParsing = $this->generateRules();
        }

        return $this->rulesForParsing;
    }

}

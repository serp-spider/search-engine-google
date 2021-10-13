<?php
namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule;

use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\ClassicalResultEngine;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical\Versions\Desktop\DesktopV1;

class AbstractRuleDesktop extends ClassicalResultEngine
{
    protected $rulesForParsing;

    protected function generateRules()
    {
        return [
            new DesktopV1()
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

<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Adwords\AdwordsItemMobile;

/**
 * Parses adwords results from a google SERP
 */
class MobileAdwordsSectionParser extends AdwordsSectionParser
{
    /**
     * @inheritdoc
     */
    protected function generateRules()
    {
        return [
            new AdwordsItemMobile()
        ];
    }
}

<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Evaluated;

use Serps\SearchEngine\Google\AdwordsResultType;
use Serps\SearchEngine\Google\AdwordsSectionResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\AbstractParser;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Adwords\AdwordsItem;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Adwords\Shopping;

/**
 * Parses adwords results from a google SERP
 */
class AdwordsSectionParser extends AbstractParser
{

    protected $pathToItems;
    protected $location;

    /**
     * @param $pathToItems
     */
    public function __construct($pathToItems, $location)
    {
        $this->pathToItems = $pathToItems;
        $this->location = $location;
    }

    protected function createResultSet(GoogleDom $googleDom)
    {
        return new AdwordsSectionResultSet($this->location);
    }


    /**
     * @inheritdoc
     */
    protected function generateRules()
    {
        return [
            new AdwordsItem(),
            new Shopping()
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getParsableItems(GoogleDom $googleDom)
    {
        $xpathObject = $googleDom->getXpath();
        $xpathElementGroups = $this->pathToItems;
        return $xpathObject->query($xpathElementGroups);
    }
}

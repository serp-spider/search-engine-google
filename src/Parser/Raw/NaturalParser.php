<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser\Raw;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\AbstractNaturalParser;
use Serps\SearchEngine\Google\Parser\Raw\Rule\ClassicalLargeVideo;
use Serps\SearchEngine\Google\Parser\Raw\Rule\ClassicalResult;
use Serps\SearchEngine\Google\Parser\Raw\Rule\ClassicalThumbVideo;
use Serps\SearchEngine\Google\Parser\Raw\Rule\ImageGroup;
use Serps\SearchEngine\Google\Parser\Raw\Rule\Map;

class NaturalParser extends AbstractNaturalParser
{

    /**
     * @inheritdoc
     */
    protected function generateRules()
    {
        return [
            new Map(),
            new ClassicalLargeVideo(),
            new ClassicalThumbVideo(),
            new ClassicalResult(),
            new ImageGroup()
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getParsableItems(GoogleDom $googleDom)
    {
        $xpathObject = $googleDom->getXpath();
        $xpathElementGroups =
            "//div[@id = 'ires']/descendant"
            . "::*[self::div or self::li][contains(concat(' ', normalize-space(@class), ' '), ' g ')]";
        return $xpathObject->query($xpathElementGroups);
    }
}

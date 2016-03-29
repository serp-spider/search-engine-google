<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google;

use Symfony\Component\CssSelector\CssSelectorConverter;

/**
 * Helper class that simplifies accesses to symfony/cssSelector
 */
abstract class Css
{

    /**
     * @var CssSelectorConverter
     */
    private static $converter;


    /**
     * @return CssSelectorConverter
     */
    private static function getConverter()
    {
        if (null == self::$converter) {
            self::$converter = new CssSelectorConverter();
        }
        return self::$converter;
    }

    /**
     * @param $css
     * @return string the xpath representation of the css string
     */
    public static function toXPath($css)
    {
        return self::getConverter()->toXPath($css);
    }
}

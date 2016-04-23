<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google;

use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\CssSelector\CssSelectorConverter;

/**
 * Helper class that simplifies accesses to symfony/cssSelector
 */
abstract class Css
{

    /**
     * @var CssSelector|CssSelectorConverter
     */
    private static $converter;


    /**
     * @return CssSelector|CssSelectorConverter
     */
    private static function getConverter()
    {
        if (null == self::$converter) {
            // We want this class to be compatible with either syfony/cssselector version 2 and 3
            if (class_exists('Symfony\Component\CssSelector\CssSelectorConverter')) {
                // Version >= 2.8
                self::$converter = new CssSelectorConverter();
            } else {
                // Version < 2.8
                self::$converter = new CssSelector();
            }
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

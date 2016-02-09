<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google;

use Serps\Core\UrlArchive;
use Serps\SearchEngine\Google\GoogleUrl;
use Serps\SearchEngine\Google\GoogleUrlTrait;

/**
 * A freezed version of a google url
 */
class GoogleUrlArchive extends UrlArchive implements GoogleUrlInterface
{
    use GoogleUrlTrait;
}

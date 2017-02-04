<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google;

use Serps\Core\UrlArchive;
use Serps\SearchEngine\Google\GoogleUrl;
use Serps\SearchEngine\Google\GoogleUrlTrait;

/**
 * A frozen version of a google url
 */
class GoogleUrlArchive extends UrlArchive implements GoogleUrlInterface
{
    use GoogleUrlTrait;

    public function __construct(
        $host = 'google.com',
        $path = '/search',
        $scheme = 'https',
        array $query = [],
        $hash = '',
        $port = null,
        $user = null,
        $pass = null
    ) {
        parent::__construct($scheme, $host, $path, $query, $hash, $port, $user, $pass);
    }

    public static function build(
        $scheme = null,
        $host = null,
        $path = null,
        array $query = [],
        $hash = null,
        $port = null,
        $user = null,
        $pass = null
    ) {
        return new static(
            $host,
            $path,
            $scheme,
            $query,
            $hash,
            $port,
            $user,
            $pass
        );
    }
}

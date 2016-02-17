<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google;


use Serps\Exception;

class InvalidDOMException extends Exception
{

    public function __construct($message)
    {
        parent::__construct($message . " Google DOM has possibly changes and maybe an update is needed.");
    }
}

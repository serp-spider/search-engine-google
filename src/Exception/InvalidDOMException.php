<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Exception;

use Serps\Exception;

class InvalidDOMException extends Exception
{

    public function __construct($message)
    {
        parent::__construct($message . ' Google DOM has possibly changed and an update may be required.');
    }
}

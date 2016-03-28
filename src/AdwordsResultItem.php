<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google;

use Serps\Core\Serp\ItemPosition;
use Serps\Core\Serp\ResultDataInterface;

class AdwordsResultItem extends ItemPosition
{

    protected $location;

    public function __construct($location, $positionOnPage, ResultDataInterface $itemData)
    {
        $this->location = $location;
        parent::__construct($positionOnPage, $positionOnPage, $itemData);
    }

    public function getTypes()
    {
        $types = parent::getTypes();
        $types[] = $this->location;
        return $types;
    }

    public function is($types)
    {
        $types = func_get_args();
        if (in_array($this->location, $types)) {
            return true;
        }

        return call_user_func_array(['parent', 'is'], $types);
    }
}

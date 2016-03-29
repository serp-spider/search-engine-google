<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\ProxyResult;
use Serps\Core\Serp\ResultDataInterface;

class AdwordsResultItem extends ProxyResult
{

    protected $location;


    /**
     * AdwordsResultItem constructor.
     * @param string $location
     * @param ResultDataInterface $itemData
     */
    public function __construct($location, ResultDataInterface $itemData)
    {
        $this->location = $location;
        parent::__construct($itemData);
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

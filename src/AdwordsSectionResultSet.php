<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google;

use Serps\Core\Serp\ItemPosition;
use Serps\Core\Serp\ResultDataInterface;
use Serps\Core\Serp\IndexedResultSet;

/**
 * @method AdwordsResultItem[] getItems()
 */
class AdwordsSectionResultSet extends IndexedResultSet
{

    protected $location;

    /**
     * @param string $location the locations of the results (top, bottom, right)
     */
    public function __construct($location)
    {
        $this->location = $location;
        parent::__construct(1);
    }


    /**
     * @param ResultDataInterface $item
     */
    public function addItem(ResultDataInterface $item)
    {
        $this->items[] = new AdwordsResultItem(
            $this->location,
            $item
        );
    }
}

<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google;

use Serps\Core\Url\SerpUrlInterface;

interface GoogleUrlInterface extends SerpUrlInterface
{

    /**
     * Get the number of the page.
     * @return int
     */
    public function getPage();

    /**
     * @return string
     */
    public function getLanguageRestriction();

    /**
     * Get the number of results per pages
     * @return int the number of results per pages
     */
    public function getResultsPerPage();
    /**
     * Get the google result type. That's the result type in the top bar 'all', 'images', 'videos'...
     * You can use the special constant beginning with ``RESULT_TYPE_`` e.g: ``GoogleUrl::RESULT_TYPE_IMAGES``
     * @return string
     */
    public function getResultType();


    public function getArchive();
}

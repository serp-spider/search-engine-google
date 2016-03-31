<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google;

use Serps\Core\Url\SerpUrlInterface;

/**
 * The only purpose of this interface is to offer a type hinting fot GoogleUrlTrait
 * Because traits are not support as type hinting
 */
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


    /**
     * @return GoogleUrlArchive
     */
    public function getArchive();

    /**
     * Check whether or not the auto correction of search term is enabled
     * @return bool
     */
    public function getAutoCorrectionEnabled();
}

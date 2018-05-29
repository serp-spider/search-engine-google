<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Page;

use Serps\Core\Dom\WebPage;
use Serps\SearchEngine\Google\Page\GoogleDom;

class GoogleError extends WebPage
{

    /**
     * @return bool Check if the page is a captcha
     */
    public function isCaptcha()
    {
        return $this->cssQuery('#recaptcha')->count() > 0;
    }
}

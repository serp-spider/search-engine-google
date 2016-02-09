<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Page;

use Serps\SearchEngine\Google\Page\GoogleDom;

class GoogleError extends GoogleDom
{

    /**
     * @return bool Check if the page is a captcha
     */
    public function isCaptcha()
    {
        $captchaQuery = "//input[@name='captcha']";
        return $this->getXpath()->query($captchaQuery)->length > 0;
    }
}

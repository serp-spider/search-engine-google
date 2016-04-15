<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Exception;

use Serps\Exception\RequestError\CaptchaException;
use Serps\SearchEngine\Google\Page\GoogleCaptcha;

/**
 * @method GoogleCaptcha getCaptcha
 */
class GoogleCaptchaException extends CaptchaException
{

    public function __construct(GoogleCaptcha $captchaResponse)
    {
        parent::__construct($captchaResponse);
    }
}

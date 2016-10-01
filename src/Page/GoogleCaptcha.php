<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Page;

use Serps\Core\Captcha\CaptchaResponse;
use Serps\Exception;
use Serps\SearchEngine\Google\Page\GoogleError;

class GoogleCaptcha implements CaptchaResponse
{

    protected $captchaImageUrlXpath = '//body/div/img';
    protected $captchaIdXpath = "//body/div/form/input[@name='id']";

    /**
     * @var GoogleError
     */
    protected $googleError;

    /**
     * GoogleCaptcha constructor.
     * @param GoogleError $googleError
     */
    public function __construct(GoogleError $googleError)
    {
        $this->googleError = $googleError;
    }

    /**
     * @return GoogleError
     */
    public function getErrorPage()
    {
        return $this->googleError;
    }

    /**
     * Gets the url of the image. Be aware that each call to this method will regenerate the captcha image
     * and the previous generated image will be invalided
     *
     * @return mixed
     * @throws Exception
     */
    public function getImageUrl()
    {
        $imageTag = $this->googleError
            ->getXpath()
            ->query($this->captchaImageUrlXpath);

        if (!$imageTag) {
            throw new Exception('Unable to find the captcha image');
        }

        $src =  $imageTag->item(0)->getAttribute('src');
        $d = $this->googleError->getUrl()->resolve($src);
        return $d->buildUrl();
    }

    public function getCaptchaType()
    {
        return self::CAPTCHA_TYPE_IMAGE;
    }

    /**
     * Gets the captcha image. Be aware that each call to this method will regenerate the captcha image
     * and the previous generated image will be invalid
     * @return string
     * @throws Exception
     */
    public function getData()
    {
        $imageUrl = $this->getImageUrl();
        return file_get_contents($imageUrl);
    }

    /**
     * The captcha resolution id to send with the form to solve the captcha
     * @return mixed
     * @throws Exception
     */
    public function getId()
    {
        $inputTag = $this->googleError
            ->getXpath()
            ->query($this->captchaIdXpath);
        if (!$inputTag) {
            throw new Exception('Unable to find the captcha image');
        }
        $id = $inputTag->item(0)->getAttribute('value');
        return $id;
    }

    public function getDetectedIp()
    {

        $ipV4 = '(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})';
        $ipV6 = '(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))';

        $regexp = "/($ipV4|$ipV6)/";
        $hasMatch = preg_match($regexp, $this->googleError->getDom()->C14N(), $match);
        if ($hasMatch) {
            return $match[1];
        } else {
            return null;
        }
    }
}

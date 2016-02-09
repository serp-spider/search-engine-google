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
     * and the previous generated image will be invalid
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
        $d = $this->googleError->getEffectiveUrl()->resolve($src);
        return $d->buildUrl();
    }

    /**
     * Gets the captcha image. Be aware that each call to this method will regenerate the captcha image
     * and the previous generated image will be invalid
     * @return string
     * @throws Exception
     */
    public function getImage()
    {
        $imageUrl = $this->getImageUrl();
        return file_get_contents($imageUrl);
    }

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
        $regexp = '/IP address: (\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/';
        $hasMatch = preg_match($regexp, $this->googleError->getDom()->C14N(), $match);
        if ($hasMatch) {
            return $match[1];
        } else {
            return null;
        }
    }
}

<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Page;

use Serps\Core\Captcha\CaptchaResponse;
use Serps\Exception;
use Serps\SearchEngine\Google\Exception\InvalidDOMException;
use Serps\SearchEngine\Google\Page\GoogleError;

class GoogleCaptcha implements CaptchaResponse
{


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
        $imageTag = $this->googleError->cssQuery('img');

        if ($imageTag->length !== 1) {
            throw new InvalidDOMException('Unable to find the captcha image.');
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
        $inputTag = $this->googleError->cssQuery('input[name="q"]');
        if ($inputTag->length == 0) {
            throw new InvalidDOMException('Unable to find the captcha id.');
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

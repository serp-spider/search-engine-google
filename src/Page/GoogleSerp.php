<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Page;

use Serps\Exception;
use Serps\SearchEngine\Google\InvalidDOMException;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\NaturalParser;

class GoogleSerp extends GoogleDom
{

    /**
     * Get the location detected by google
     * @return string
     */
    public function getLocation()
    {
        $locationRegexp = '#"uul_text":"([^"]+)"#';

        preg_match($locationRegexp, $this->dom->C14N(), $matches);

        if ($matches && isset($matches[1])) {
            return $matches[1];
        }
        return null;
    }

    /**
     * @return \Serps\Core\Serp\ResultSet
     */
    public function getNaturalResults()
    {
        if($this->javascriptIsEvaluated()){
            $parser = new NaturalParser();
        }else{
            // TODO
            throw new \Exception("Google parser does not currently support parsing non javascript results");
        }
        return $parser->parse($this);
    }


    public function getAdwordsResults()
    {
        // TODO
        throw  new Exception('Not implemented');
    }

    public function javascriptIsEvaluated()
    {
        $body = $this->getXpath()->query('//body');

        if ($body->length != 1) {
            throw new Exception('No body found');
        }

        $body = $body->item(0);
        /** @var $body \DOMElement */
        $class = $body->getAttribute('class');
        if ($class=='hsrp') {
            return false;
        } elseif (strstr($class, 'srp')) {
            return true;
        } else {
            throw new InvalidDOMException('Unable to check javascript status.');
        }
    }
}

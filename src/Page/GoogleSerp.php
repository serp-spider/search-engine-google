<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Page;

use Serps\Exception;
use Serps\SearchEngine\Google\InvalidDOMException;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\Evaluated\AdwordsParser;
use Serps\SearchEngine\Google\Parser\Evaluated\NaturalParser as EvaluatedNaturalParser;
use Serps\SearchEngine\Google\Parser\Raw\NaturalParser as RawNaturalParser;

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
     * @return \Serps\Core\Serp\IndexedResultSet
     */
    public function getNaturalResults()
    {
        if ($this->javascriptIsEvaluated()) {
            $parser = new EvaluatedNaturalParser();
        } else {
            $parser = new RawNaturalParser();
        }
        return $parser->parse($this);
    }


    /**
     * @return \Serps\Core\Serp\CompositeResultSet
     * @throws Exception
     * @throws InvalidDOMException
     */
    public function getAdwordsResults()
    {
        if ($this->javascriptIsEvaluated()) {
            $parser = new AdwordsParser();
            return $parser->parse($this);
        } else {
            throw new Exception('Adwords parser is not available for non evaluated results');
        }
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

<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Page;

use Serps\Exception;
use Serps\SearchEngine\Google\Exception\InvalidDOMException;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\Evaluated\AdwordsParser as EvaluatedAdwordsParser;
use Serps\SearchEngine\Google\Parser\Raw\AdwordsParser as RawAdwordsParser;
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
            $parser = new EvaluatedAdwordsParser();
            return $parser->parse($this);
        } else {
            $parser = new RawAdwordsParser();
            return $parser->parse($this);
        }
    }

    /**
     * Get the total number of results available for the search terms
     * @return int the number of results
     * @throws InvalidDOMException
     */
    public function getNumberOfResults()
    {
        $item = $this->cssQuery('#resultStats');
        if ($item->length != 1) {
            return null;
        }
        $nodeText = $item->item(0)->childNodes->item(0);

        if (!$nodeText) {
            return null;
        }

        // WARNING: The number of result is explained in different format according to the country. Fon instance:
        // UK:  6,200,000
        // FR:  6 200 000
        // DE:  2.200.000
        // IN:  62,00,000
        // We have to use a global matcher
        $matched = preg_match('/([0-9]+[ \.,\x{00a0}])+/u', $nodeText->textContent, $countMatch);

        if (!$matched) {
            return null;
        }

        return (int) preg_replace('/[^0-9]/', '', $countMatch[0]);
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

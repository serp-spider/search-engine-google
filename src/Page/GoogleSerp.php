<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Page;

use Serps\Exception;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\NaturalParser;

class GoogleSerp extends GoogleDom
{

    /**
     * Get the total number of results
     * @return int the number of results
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

    public function getNaturalResults()
    {
        $parser = new NaturalParser();
        return $parser->parse($this);
    }

    public function getAdwordsResults()
    {
        // TODO
        throw  new Exception('Not implemented');
    }
}

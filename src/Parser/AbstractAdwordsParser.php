<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser;

use Serps\Core\Serp\CompositeResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;

abstract class AbstractAdwordsParser implements ParserInterface
{

    /**
     * @var ParserInterface[]
     */
    private $parsers = null;

    /**
     * Generate a list of parsers to be used when parsing dom
     * @return ParserInterface[]
     */
    abstract public function generateParsers();

    /**
     * @return ParserInterface[]
     */
    public function getParsers()
    {
        if (null == $this->parsers) {
            $this->parsers = $this->generateParsers();
        }
        return $this->parsers;
    }

    /**
     * @inheritdoc
     */
    public function parse(GoogleDom $googleDom)
    {
        $resultsSets = new CompositeResultSet();

        $parsers = $this->getParsers();

        foreach ($parsers as $parser) {
            $resultsSets->addResultSet(
                $parser->parse($googleDom)
            );
        }

        return $resultsSets;
    }
}

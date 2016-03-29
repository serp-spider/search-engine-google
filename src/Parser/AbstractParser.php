<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser;

use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Page\GoogleDom;

abstract class AbstractParser
{

    /**
     * @var ParsingRuleInterace[]
     */
    protected $rules = null;

    /**
     * @return ParsingRuleInterace[]
     */
    abstract protected function generateRules();

    abstract protected function getParsableItems(GoogleDom $googleDom);


    /**
     * @return ParsingRuleInterace[]
     */
    public function getRules()
    {
        if (null == $this->rules) {
            $this->rules = $this->generateRules();
        }
        return $this->rules;
    }

    /**
     * Parses the given google dom
     * @param GoogleDom $googleDom
     * @return IndexedResultSet
     */
    public function parse(GoogleDom $googleDom)
    {
        $elementGroups = $this->getParsableItems($googleDom);
        $resultSet = $this->createResultSet($googleDom);
        return $this->parseGroups($elementGroups, $resultSet, $googleDom);
    }

    protected function createResultSet(GoogleDom $googleDom)
    {
        $startingAt = $googleDom->getUrl()->getResultsPerPage() * $googleDom->getUrl()->getPage();
        return new IndexedResultSet($startingAt);
    }

    /**
     * @param $elementGroups
     * @param IndexedResultSet $resultSet
     * @param $googleDom
     * @return IndexedResultSet
     */
    protected function parseGroups($elementGroups, IndexedResultSet $resultSet, $googleDom)
    {
        $rules = $this->getRules();

        foreach ($elementGroups as $group) {
            foreach ($rules as $rule) {
                $match = $rule->match($googleDom, $group);
                if ($match instanceof \DOMNodeList) {
                    $this->parseGroups($group->childNodes, $resultSet, $googleDom);
                    break;
                } else {
                    switch ($match) {
                        case ParsingRuleInterace::RULE_MATCH_MATCHED:
                            $rule->parse($googleDom, $group, $resultSet);
                            break 2;
                        case ParsingRuleInterace::RULE_MATCH_STOP:
                            break 2;
                    }
                }
            }
        }
        return $resultSet;
    }
}

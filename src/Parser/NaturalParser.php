<?php
/**
 * @license see LICENSE
 */

namespace Serps\SearchEngine\Google\Parser;

use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\ResultSet;
use Serps\SearchEngine\Google\Parser\Rule\ClassicalResult;
use Serps\SearchEngine\Google\Parser\Rule\Divider;
use Serps\SearchEngine\Google\Parser\Rule\ImageGroup;
use Serps\SearchEngine\Google\Parser\Rule\InTheNews;
use Serps\SearchEngine\Google\Parser\Rule\ParsingRuleInterace;
use Serps\SearchEngine\Google\Parser\Rule\SearchResultGroup;
use Serps\SearchEngine\Google\Parser\Rule\TweetsCarousel;
use Serps\SearchEngine\Google\Parser\Rule\TweetsResult;
use Serps\SearchEngine\Google\Parser\Rule\Video;

/**
 * Parses natural results from a google SERP
 */
class NaturalParser
{

    /**
     * @var ParsingRuleInterace[]
     */
    protected $rules;

    /**
     * NaturalParser constructor.
     */
    public function __construct()
    {
        $this->rules = [
            new Divider(),
            new ClassicalResult(),
            new SearchResultGroup(),
            new TweetsCarousel(),
            new ImageGroup(),
            new Video(),
            new InTheNews()
        ];
    }


    /**
     * @param \Serps\SearchEngine\Google\Page\GoogleDom $googleDom
     * @return ResultSet $resultSet
     */
    public function parse(GoogleDom $googleDom)
    {
        $xpathObject = $googleDom->getXpath();
        $xpathElementGroups = "//div[@id = 'ires']/ol/*";
        $elementGroups = $xpathObject->query($xpathElementGroups);

        $startingAt = $googleDom->getUrl()->getResultsPerPage() * $googleDom->getUrl()->getPage();
        $resultSet = new ResultSet($startingAt);

        return $this->parseGroups($elementGroups, $resultSet, $googleDom);
    }

    /**
     * @param $elementGroups
     * @param ResultSet $resultSet
     * @param $googleDom
     * @return ResultSet
     */
    protected function parseGroups($elementGroups, ResultSet $resultSet, $googleDom)
    {
        foreach ($elementGroups as $group) {
            foreach ($this->rules as $rule) {
                $match = $rule->match($group);
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

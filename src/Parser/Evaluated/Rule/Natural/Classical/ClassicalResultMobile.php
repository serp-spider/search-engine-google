<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical;

use Serps\Core\Dom\DomElement;
use Serps\Core\Dom\DomNodeList;
use Serps\SearchEngine\Google\Exception\InvalidDOMException;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\AbstractRuleMobile;
use Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\SiteLinksBigMobile;
use Serps\SearchEngine\Google\Parser\ParsingRuleByVersionInterface;
use Serps\SearchEngine\Google\Parser\ParsingRuleInterface;
use Serps\SearchEngine\Google\NaturalResultType;

class ClassicalResultMobile extends AbstractRuleMobile implements ParsingRuleInterface
{
    public function match(GoogleDom $dom, DomElement $node)
    {
        if ($node->getAttribute('id') == 'rso') {
            return self::RULE_MATCH_MATCHED;
        }

        return self::RULE_MATCH_NOMATCH;
    }

    protected function parseNode(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet)
    {
        $naturalResults = $dom->xpathQuery("descendant::div[@class='mnr-c xpd O9g5cc uUPGi']", $node);

        if ($naturalResults->length == 0) {
            throw new InvalidDOMException('Cannot parse a classical result.');
        }

        $k = 0;
        foreach ($naturalResults as $organicResult) {
            $k++;
            $result = null;

            /** @var ParsingRuleByVersionInterface $rule */
            foreach ($this->getRules() as $versionRule) {
                $organicResultObject = new OrganicResultObject();

                try {
                    $versionRule->parseNode($dom, $organicResult, $organicResultObject);

                    break 1;
                } catch (\Exception $exception) {
                    continue;
                } catch (\Error $exception) {
                    continue;
                }
            }

            if ($organicResultObject->getLink() === null) {
                throw new \Exception('bla bla');
            }

            $resultSet->addItem(new BaseResult([NaturalResultType::CLASSICAL_MOBILE],
                [
                    'title'       => $organicResultObject->getTitle(),
                    'url'         => $organicResultObject->getLink(),
                    'description' => $organicResultObject->getDescription(),
                ]
            ));

            if ($dom->xpathQuery("descendant::div[@class='MUxGbd v0nnCb lyLwlc']", $organicResult->parentNode->parentNode)->length > 0) {
                (new SiteLinksBigMobile())->parse($dom, $organicResult->parentNode->parentNode, $resultSet, false);
            }
        }
    }

    public function parse(GoogleDom $dom, \DomElement $node, IndexedResultSet $resultSet, $isMobile = false)
    {
        $this->parseNode($dom, $node, $resultSet);
    }
}

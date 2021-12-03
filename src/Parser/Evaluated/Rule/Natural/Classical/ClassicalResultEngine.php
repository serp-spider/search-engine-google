<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleByVersionInterface;

class ClassicalResultEngine
{
    protected $resultType = NaturalResultType::CLASSICAL;

    protected function parseNode(GoogleDom $dom, \DomElement $organicResult, IndexedResultSet $resultSet, $k)
    {

    }

    protected function parseNodeWithRules(GoogleDom $dom, \DomElement $organicResult, IndexedResultSet $resultSet, $k)
    {
        $organicResultObject = new OrganicResultObject();

        /** @var ParsingRuleByVersionInterface $versionRule */
        foreach ($this->getRules() as $versionRule) {

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

        $resultSet->addItem(new BaseResult([$this->resultType],
            [
                'title'       => $organicResultObject->getTitle(),
                'url'         => $organicResultObject->getLink(),
                'description' => $organicResultObject->getDescription(),
            ]
        ));
    }
}
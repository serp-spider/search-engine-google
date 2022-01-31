<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleByVersionInterface;

class ClassicalResultEngine
{
    use \Serps\SearchEngine\Google\Parser\Helper\Log;

    protected $resultType = NaturalResultType::CLASSICAL;


    public function __construct()
    {
        $this->initLogger();
    }

    protected function parseNode(GoogleDom $dom, \DomElement $organicResult, IndexedResultSet $resultSet, $k) {}

    protected function parseNodeWithRules(GoogleDom $dom, \DomElement $organicResult, IndexedResultSet $resultSet, $k)
    {
        $organicResultObject = new OrganicResultObject();

        /** @var ParsingRuleByVersionInterface $versionRule */
        foreach ($this->getRules() as $versionRule) {

            try {
                $versionRule->parseNode($dom, $organicResult, $organicResultObject);
            } catch (\Throwable $exception) {
                continue;
            }
        }

        if ($organicResultObject->getLink() === null || $organicResultObject->getTitle() === null) {

            $resultSet->addItem(new BaseResult(NaturalResultType::EXCEPTIONS, []));
            //$this->monolog->error('Cannot identify natural result ', ['html'=>$organicResult->ownerDocument->saveHTML($organicResult), ]);

            return;
        }

        if (strpos($organicResultObject->getLink(), 'google.com') !== false) {
            return;
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

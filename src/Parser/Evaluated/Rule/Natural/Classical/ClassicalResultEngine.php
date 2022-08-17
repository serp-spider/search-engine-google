<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical;

use Monolog\Logger;
use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleByVersionInterface;

class ClassicalResultEngine
{
    use \Serps\SearchEngine\Google\Parser\Helper\Log;

    protected $resultType = NaturalResultType::CLASSICAL;

    /**
     * @param Logger|null $logger Monolog log channel dependency
     */
    public function __construct(Logger $logger = null)
    {
        $this->initLogger($logger);
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

            $resultSet->addItem(new BaseResult(NaturalResultType::EXCEPTIONS, [], $organicResult));
            //$this->monolog->error('Cannot identify natural result', ['class' => self::class]);

            return;
        }

        if (strpos($organicResultObject->getLink(), 'google.') !== false && strpos($organicResultObject->getLink(), '/search') !== false ) {
            return;
        }

        $resultSet->addItem(new BaseResult(
            [$this->resultType],
            [
                'title'       => $organicResultObject->getTitle(),
                'url'         => $organicResultObject->getLink(),
                'description' => $organicResultObject->getDescription(),
            ],
            $organicResult
        ));
    }
}

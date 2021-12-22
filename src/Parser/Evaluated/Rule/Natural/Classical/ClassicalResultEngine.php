<?php

namespace Serps\SearchEngine\Google\Parser\Evaluated\Rule\Natural\Classical;

use Serps\Core\Serp\BaseResult;
use Serps\Core\Serp\IndexedResultSet;
use Serps\SearchEngine\Google\NaturalResultType;
use Serps\SearchEngine\Google\Page\GoogleDom;
use Serps\SearchEngine\Google\Parser\ParsingRuleByVersionInterface;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SyslogUdpHandler;

class ClassicalResultEngine
{
    protected $resultType = NaturalResultType::CLASSICAL;

    /** @var Logger | null */
    protected $monolog = null;

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

                break 1;
            } catch (\Exception $exception) {
                continue;
            } catch (\Error $exception) {
                continue;
            }
        }

        if ($organicResultObject->getLink() === null) {

            $this->monolog->error('Cannot identify natural result ', ['html'=>$organicResult->ownerDocument->saveHTML($organicResult), ]);

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

    protected function initLogger()
    {
        if($this->monolog !== null) {
           return;
        }

        $this->monolog  = new Logger('data-integrity');
        $syslog_handler = new SyslogUdpHandler("logs.papertrailapp.com", 35320, LOG_USER, Logger::WARNING);
        $syslog_handler->setFormatter(new LineFormatter("%channel%.%level_name%: %message%"));
        $this->monolog->pushHandler($syslog_handler);
    }
}

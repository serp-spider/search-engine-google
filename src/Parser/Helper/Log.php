<?php

namespace Serps\SearchEngine\Google\Parser\Helper;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FilterHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Logger;

/**
 * Logging trait
 *
 * Make sure you're calling self::initLogger() before using the $this->monolog property!
 */
trait Log
{
    /** @var Logger | null */
    protected $monolog = null;

    /**
     * @param Logger|null $logger Monolog log channel dependency; If not provided, defaults to logging to stdout
     * @return void
     */
    protected function initLogger(Logger $logger = null)
    {
        if ($logger === null) {
            $logger = $this->buildDefaultLogger();
        }

        $this->monolog = $logger;
    }

    /**
     * Builds a default logger instance - outputting errors on stderr and lower level messages to stdout
     * @return Logger
     */
    protected function buildDefaultLogger(): Logger
    {
        $logger = new Logger('serpsDefaultLogger');

        $errorHandler = new StreamHandler('php://stderr', Logger::ERROR);
        $nonErrorHandler = new FilterHandler(
            new StreamHandler('php://stdout', Logger::DEBUG),
            Logger::DEBUG,
            Logger::WARNING
        );

        $logger->setHandlers([
            $errorHandler,
            $nonErrorHandler
        ]);

        return $logger;
    }
}

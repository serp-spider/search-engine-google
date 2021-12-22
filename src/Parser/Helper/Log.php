<?php

namespace Serps\SearchEngine\Google\Parser\Helper;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Logger;

trait Log
{

    /** @var Logger | null */
    protected $monolog = null;

    protected function initLogger()
    {
        if ($this->monolog !== null) {
            return;
        }

        $this->monolog  = new Logger('serp-features-parser');
        $syslog_handler = new SyslogUdpHandler("logs.papertrailapp.com", 35320, LOG_USER, Logger::WARNING);
        $syslog_handler->setFormatter(new LineFormatter("%channel%.%level_name%: %message%"));
        $this->monolog->pushHandler($syslog_handler);
    }
}

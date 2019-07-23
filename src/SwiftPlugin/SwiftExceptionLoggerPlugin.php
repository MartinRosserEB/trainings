<?php

namespace App\SwiftPlugin;

use Psr\Log\LoggerAwareInterface;

class SwiftExceptionLoggerPlugin implements \Swift_Plugins_Logger, \Swift_Events_EventListener, LoggerAwareInterface
{
    private $logSrv;

    public function setLogger($logger)
    {
        $this->logSrv = $logger;
    }

    public function add($entry)
    {
        $this->logSrv->warning(sprintf("Swiftmailer: %s", $entry));
    }

    public function clear() {}
    public function dump() {}
}
<?php

namespace Otus\Diag;

use Bitrix\Main\Diag\ExceptionHandlerFormatter;
use Bitrix\Main\Diag\FileExceptionHandlerLog;

class FileExceptionHandlerLogCustom extends FileExceptionHandlerLog
{
    public function write($exception, $logType)
    {
        $text = ExceptionHandlerFormatter::format($exception, false, $this->level);

        $context = [
            'type' => static::logTypeToString($logType),
        ];

        $logLevel = static::logTypeToLevel($logType);

        $message = "OTUS: {date} - Host: {host} - {type} - {$text}\n";

        $this->logger->log($logLevel, $message, $context);
    }
}

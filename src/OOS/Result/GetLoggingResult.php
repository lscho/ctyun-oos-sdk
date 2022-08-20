<?php

namespace OOS\Result;

use OOS\Model\LoggingConfig;


/**
 * Class GetLoggingResult
 * @package OOS\Result
 */
class GetLoggingResult extends Result
{
    /**
     * Parse LoggingConfig data
     *
     * @return LoggingConfig
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $config = new LoggingConfig();
        $config->parseFromXml($content);
        return $config;
    }

}
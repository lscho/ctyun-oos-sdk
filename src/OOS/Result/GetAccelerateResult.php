<?php

namespace OOS\Result;


use OOS\Model\AccelerateConfig;

/**
 * Class GetAccelerateResult
 * @package OOS\Result
 */
class GetAccelerateResult extends Result
{
    /**
     * Parse AccelerateConfig data
     *
     * @return AccelerateConfig
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $config = new AccelerateConfig();
        $config->parseFromXml($content);
        return $config;
    }

}
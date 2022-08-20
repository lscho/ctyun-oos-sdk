<?php

namespace OOS\Result;

use OOS\Model\WebsiteConfig;

/**
 * Class GetWebsiteResult
 * @package OOS\Result
 */
class GetWebsiteResult extends Result
{
    /**
     * Parse WebsiteConfig data
     *
     * @return WebsiteConfig
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $config = new WebsiteConfig();
        $config->parseFromXml($content);
        return $config;
    }


}
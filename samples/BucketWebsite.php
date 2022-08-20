<?php
require_once __DIR__ . '/Common.php';

use OOS\OosClient;
use OOS\Core\OosException;
use OOS\Model\WebsiteConfig;

$bucket = Common::getBucketName();
$oosClient = Common::getOosClient();
if (is_null($oosClient)) exit(1);

/**
 * Sets bucket static website configuration
 *
 * @param $oosClient OosClient
 * @param  $bucket string bucket name
 * @return null
 */
function putBucketWebsite($oosClient, $bucket)
{
    $websiteConfig = new WebsiteConfig();
    $websiteConfig->setIndexDocument("index.html");
    //$websiteConfig->setErrorDocument("error.html");
    try {
        $oosClient->putBucketWebsite($bucket, $websiteConfig);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get bucket static website configuration
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws
 */
function getBucketWebsite($oosClient, $bucket)
{
    $websiteConfig = null;
    try {
        $websiteConfig = $oosClient->getBucketWebsite($bucket);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print($websiteConfig->serializeToXml() . "\n");
}

/**
 * Delete bucket static website configuration
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteBucketWebsite($oosClient, $bucket)
{
    try {
        $oosClient->deleteBucketWebsite($bucket);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


//******************************* For complete usage, see the following functions  ****************************************************


//9	Bucket	Put Bucket WebSite	PUT	设置website配置。如果bucket已经存在了website，此操作会替换原有website。
//只有bucket的owner才能执行此操作，否则会返回403 AccessDenied错误。
putBucketWebsite($oosClient, $bucket);

//10	Bucket	Get Bucket WebSite	GET 	获得指定bucket的website。只有bucket的owner才能执行此操作，
//否则会返回403 AccessDenied错误。
getBucketWebsite($oosClient, $bucket);


//11	Bucket	Delete bucket WebSite	DELETE	删除指定bucket的website。只有bucket的owner才能执行此操作，
//否则会返回403 AccessDenied错误。如果bucket没有website，返回200 OK
//deleteBucketWebsite($oosClient, $bucket);


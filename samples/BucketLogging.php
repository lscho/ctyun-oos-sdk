<?php
require_once __DIR__ . '/Common.php';

use OOS\OosClient;
use OOS\Core\OosException;

$bucket = Common::getBucketName();
$oosClient = Common::getOosClient();
if (is_null($oosClient)) exit(1);

//*******************************Simple Usage ***************************************************************

/**
 * Set bucket logging configuration
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function putBucketLogging($oosClient, $bucket)
{
    $option = array();
    // Access logs are stored in the same bucket.
    $targetBucket = $bucket;
    $targetPrefix = "access.log";

    try {
        $oosClient->putBucketLogging($bucket, $targetBucket, $targetPrefix, $option);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get bucket logging configuration
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getBucketLogging($oosClient, $bucket)
{
    $loggingConfig = null;
    $options = array();
    try {
        $loggingConfig = $oosClient->getBucketLogging($bucket, $options);
        $loggingConfig->getTargetBucket();
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print($loggingConfig->serializeToXml() . "\n");
}



//******************************* For complete usage, see the following functions ****************************************************

//13	Bucket	Put Bucket Logging	PUT	在PUT操作的url中加上logging，可以进行添加/修改/删除logging的操作。
//如果bucket已经存在了logging，此操作会替换原有logging。
//只有bucket的owner才能执行此操作，否则会返回403 AccessDenied错误。
putBucketLogging($oosClient, $bucket);

//14	Bucket	Get Bucket Logging	GET 	此操作用于判断bucket是否存在，而且用户是否有权限访问。
//如果bucket存在，而且用户有权限访问时，此操作返回200 OK。否则，返回404 不存在，或者403 没有权限。
getBucketLogging($oosClient, $bucket);


<?php
require_once __DIR__ . '/Common.php';

use OOS\OosClient;
use OOS\Core\OosException;
use OOS\Model\CorsConfig;
use OOS\Model\CorsRule;

$oosClient = Common::getOosClient();
if (is_null($oosClient)) exit(1);
$bucket = Common::getBucketName();


//******************************* Simple usage****************************************************************
/*
// Set cors configuration
$corsConfig = new CorsConfig();
$rule = new CorsRule();
$rule->addAllowedHeader("x-amz-header");
$rule->addAllowedOrigin("http://www.b.com");
$rule->addAllowedMethod("POST");
$rule->setMaxAgeSeconds(10);
$corsConfig->addRule($rule);
$oosClient->putBucketCors($bucket, $corsConfig);
Common::println("bucket $bucket corsConfig created:" . $corsConfig->serializeToXml());

// Get cors configuration
$corsConfig = $oosClient->getBucketCors($bucket);
Common::println("bucket $bucket corsConfig fetched:" . $corsConfig->serializeToXml());

// Delete cors configuration
$oosClient->deleteBucketCors($bucket);
Common::println("bucket $bucket corsConfig deleted");
*/


/**
 * Set bucket cores
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @throws
 * @return null
 */
function putBucketCors($oosClient, $bucket)
{
    $corsConfig = new CorsConfig();
    $rule = new CorsRule();
    $rule->addAllowedHeader("x-amz-header");
    $rule->addAllowedOrigin("http://www.b.com");
    $rule->addAllowedMethod("POST");
    $rule->setMaxAgeSeconds(10);
    $corsConfig->addRule($rule);

    try {
        $oosClient->putBucketCors($bucket, $corsConfig);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get and print the cors configuration of a bucket
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getBucketCors($oosClient, $bucket)
{
    $corsConfig = null;
    try {
        $corsConfig = $oosClient->getBucketCors($bucket);
        $rules = $corsConfig->getRules();
        foreach ($rules as $rule){
            $rule->getId();
            foreach ($rule->getExposeHeaders() as $exposeHeader){
                printf($exposeHeader . "\n");
            }
        }
    } catch (OosException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print($corsConfig->serializeToXml() . "\n");
}

/**
 * Delete all cors configuraiton of a bucket
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteBucketCors($oosClient, $bucket)
{
    try {
        $oosClient->deleteBucketCors($bucket);
    } catch (OosException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}



//******************************* For complete usage, see the following functions  *****************************************************
//21	Bucket	PUT Bucket cors	PUT	通过 CORS ，客户可以构建丰富的客户端 Web 应用程序，同时可以选择性地允许跨域访问OOS 资源
putBucketCors($oosClient, $bucket);

//22	Bucket	GET Bucket cors	GET 	返回bucket的跨域配置信息
getBucketCors($oosClient, $bucket);

//23	Bucket	DELETE Bucket cors	DELETE	删除bucket的跨域配置信息
deleteBucketCors($oosClient, $bucket);

<?php
require_once __DIR__ . '/Common.php';

use OOS\Http\RequestCore;
use OOS\Http\ResponseCore;
use OOS\OosClient;
use OOS\Core\OosException;

$bucket = Common::getBucketName();
$oosClient = Common::getOosClient();
if (is_null($oosClient)) exit(1);

//******************************* Simple Usage ***************************************************************
/**
 * Generate the signed url for getObject() to control read accesses under private privilege
 *
 * @param $oosClient OosClient OosClient instance
 * @param $bucket string bucket name
 * @return null
 */
function getSignedUrlForGettingObject($oosClient, $bucket)
{
    $object = "object.php";
    $timeout = 3600 * 24;
    //限速KB
    $options[OosClient::OOS_LIMITRATE] = "1024";
    try {
        $signedUrl = $oosClient->signUrl($bucket, $object, $timeout,OosClient::OOS_HTTP_GET,$options);
    } catch (OosException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }

    print(__FUNCTION__ . ": signedUrl: " . $signedUrl . "\n");
}
//******************************* For complete usage, see the following functions ****************************************************
//16	Object	生成共享链接	对于私有或只读Bucket，可以通过生成Object的共享链接的方式，
//将Object分享给其他人，同时可以在链接中设置限速以对下载速度进行控制。
getSignedUrlForGettingObject($oosClient, $bucket);

<?php
require_once __DIR__ . '/Common.php';
header("Content-type: text/html; charset=utf-8");

use OOS\OosClient;
use OOS\Core\OosException;
use OOS\Model\DataLocation;
use OOS\Core\OosUtil;


$bucket = Common::getBucketName();
$oosClient = Common::getOosClient();
if (is_null($oosClient)) exit(1);
//******************************* Simple usage ***************************************************************

/**
 * Create a 'virtual' folder
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function Test()
{
    try {
//        $canonicalRequest = $httpMethod . "\n"
//            . $canonicalURI . "\n"
//            . $canonicalQueryString . "\n"
//            . $canonicalHeaders . "\n"
//            . $signedHeaders . "\n"
//            . $payload;

        $canonicalRequest = "POST" . "\n"
            . "/" . "\n"
            . "" . "\n"
            . "host:oos-xxx-iam.ctyunapi.cn:8082" . "\n"
            . "x-amz-content-sha256:038f209b1a6f0652a19089af9b13df4b51508963f3ac5dfbd6f8ae5c0a494b2f" . "\n"
            . "x-amz-date:20190820T064600Z" . "\n"
            . "" . "\n"
            . "host;x-amz-content-sha256;x-amz-date" . "\n"
            . "038f209b1a6f0652a19089af9b13df4b51508963f3ac5dfbd6f8ae5c0a494b2f";
        $canonicalRequestHash = hash("sha256",$canonicalRequest,false);
        $a= 1;

    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print($canonicalRequestHash . ": OK" . "\n");
}

Test();

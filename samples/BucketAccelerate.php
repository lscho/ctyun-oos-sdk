<?php
require_once __DIR__ . '/Common.php';

use OOS\OosClient;
use OOS\Core\OosException;
use \OOS\Model\AccelerateConfig;

$bucket = Common::getBucketName();
$oosClient = Common::getOosClient();
if (is_null($oosClient)) exit(1);

/**
 * Set bucket Accelerate
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function putBucketAccelerate($oosClient, $bucket)
{
    $AccelerateConfig = new AccelerateConfig();
    try {
        $AccelerateConfig->setStatus("Enabled");
       // $AccelerateConfig->addIPWhiteLists("36.111.88.0/24");
       // $AccelerateConfig->addIPWhiteLists("114.80.1.136");

        $oosClient->putBucketAccelerate($bucket, $AccelerateConfig);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get bucket Accelerate configuration
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */

function getBucketAccelerate($oosClient, $bucket)
{
    $AccelerateConfig = null;
    try {
        $accelerateConfig = $oosClient->getBucketAccelerate($bucket);
        $accelerateConfig->getStatus();
        $accelerateConfig->getIPWhiteLists();
    } catch (OosException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print($accelerateConfig->serializeToXml() . "\n");
    //$accelerateConfig->getRefererList();
}
//******************************* For complete usage, see the following functions ****************************************************
//19	Bucket	Put Bucket accelerate	PUT	在PUT操作的url中加上accelerate，可以进行添加或修改CDN IP白名单的操作
putBucketAccelerate($oosClient, $bucket);

//20	Bucket	Get Bucket accelerate	GET 	在GET操作的url中加上accelerate，可以获得指定bucket的cdn配置信息
//getBucketAccelerate($oosClient, $bucket);


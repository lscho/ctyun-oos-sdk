<?php
require_once __DIR__ . '/Common.php';

use OOS\OosClient;
use OOS\Core\OosException;

$oosClient = Common::getOosClient();

if (is_null($oosClient)) exit(1);


//################################################################################
/**
 * List all buckets
 *
 * @param OosClient $oosClient OosClient instance
 * @return null
 */
function listBuckets($oosClient)
{
    $options = array();
    try {
        $bucketListInfo = $oosClient->listBuckets($options);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    $bucketList = $bucketListInfo->getBucketList();
    $owner = $bucketListInfo->getOwner();
    foreach ($bucketList as $bucket) {
        print( $bucket->getName() . "\t" . $bucket->getCreatedate() . "\n");
    }
}

/**
 * Get all regions
 *   Get the data regions and metadata regions of the specified user.
 *   This method is only used for Object Storage Network,
 *   the other resource pools can not use this method.
 * @param OosClient $oosClient OosClient instance
 * @return null
 * @throws
 */
function getRegions($oosClient)
{
    $options = array();

    try {
        $bucketRegions = $oosClient->getRegions($options);
        $metaRegions = $bucketRegions->getMetaRegions();
        $dataRegions = $bucketRegions->getDataRegions();
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        print(__FUNCTION__ . ": FAILED" . "\n");
        return;
    }

    print(__FUNCTION__ . ": OK" . "\n");
}

//################################################################################


//******************************* For complete usage, see the following functions ****************************************************

//1 #############################
//1Service	GetService(ListBucket)
//	对于做Get请求的服务，返回请求者拥有的所有Bucket，其中“/”表示根目录。该API只对验证用户有效，匿名用户不能执行该操作。
//listBuckets($oosClient);

//2	Service	Get Regions	获取资源池中的索引位置和数据位置列表
getRegions($oosClient);







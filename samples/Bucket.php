<?php
require_once __DIR__ . '/Common.php';
require('../src/OOS/Model/BucketDetailInfo.php');
require('../src/OOS/Model/DataLocation.php');
require('../src/OOS/Config/Config.php');

use OOS\Model\BucketDetailInfo;
use OOS\Model\MetadataLocationConstraint;
use OOS\Model\DataLocation;
use OOS\Model\Owner;
use OOS\Model\Grantee;

use OOS\OosClient;
use OOS\Core\OosException;
use OOS\Model\UploadInfo;

$oosClient = Common::getOosClient();
if (is_null($oosClient)) exit(1);
$bucket = Common::getBucketName();

/**
 * Create a new bucket
 * acl indicates the access permission of a bucket, including: private, public-read-only/private-read-write, and public read-write.
 * Private indicates that only the bucket owner or authorized users can access the data..
 * The three permissions are separately defined by (OosClient::OOS_ACL_TYPE_PRIVATE,OosClient::OOS_ACL_TYPE_PUBLIC_READ, OosClient::OOS_ACL_TYPE_PUBLIC_READ_WRITE)
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket Name of the bucket to create
 * @throws
 * @return null
 * @note 本例子只能在版本5的资源池上使用
 */
function createBucket($oosClient, $bucket)
{
    try {
        $options = array();
        //本接口只能在版本5的资源池上使用，最后一个参数必须是null
        $oosClient->createBucket($bucket, OosClient::OOS_ACL_TYPE_PUBLIC_READ_WRITE,$options,null);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Create a new bucket to location
 * acl indicates the access permission of a bucket, including: private, public-read-only/private-read-write, and public read-write.
 * Private indicates that only the bucket owner or authorized users can access the data..
 * The three permissions are separately defined by (OosClient::OOS_ACL_TYPE_PRIVATE,OosClient::OOS_ACL_TYPE_PUBLIC_READ, OosClient::OOS_ACL_TYPE_PUBLIC_READ_WRITE)
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket Name of the bucket to create
 * @throws
 * @return null
 * @note 本例子只能在版本6的资源池上使用，不能在版本5的资源池使用
 */
function createBucketWithLocation($oosClient, $bucket)
{
    try {
        $options = array();
        //当只有一个元数据和数据域时候，不能设置metadataLocationConstraint和dataLocationConstraint
        //目前可用的有ZhengZhou|ShenYang|ChengDu|WuLuMuQi|LanZhou|QingDao|GuiYang|LaSa|WuHu|WuHan|ShenZhen
        //但是要根据用户权限调用 Service.getRegions 查看可用的资源池
        $metadataLocationConstraint = new MetadataLocationConstraint("HaiKou");
        $type = "Local";
        $locationList = array();
        $locationList["HaiKou1"] = "HaiKou1";
        //$locationList["ChengDu"] = "ChengDu";
        $scheduleStrategy = "Allowed";
        $dataLocationConstraint = new DataLocation($type,$locationList,$scheduleStrategy);
        $bucketDetailInfo = new BucketDetailInfo();
        $bucketDetailInfo->setMetaLocation($metadataLocationConstraint);
        $bucketDetailInfo->setDataLocation($dataLocationConstraint);

        //本接口只能在版本6的资源池上使用，必须提供BucketDetailInfo的对象
        $oosClient->createBucket($bucket, OosClient::OOS_ACL_TYPE_PUBLIC_READ_WRITE,$options,$bucketDetailInfo);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}
//################################################################################
/**
 * Check whether a bucket exists.
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 */
function getBucketLocation($oosClient, $bucket)
{
    try {
        $options = array();
        $bucketDetailInfo = $oosClient->getBucketLocation($bucket,$options);
        $dataLocation = $bucketDetailInfo->getDataLocation();
        $metaLocation = $bucketDetailInfo->getMetaLocation();

        if(isset($dataLocation)){
            $bucketDetailInfo->getDataLocation()->getLocationList();
            $bucketDetailInfo->getDataLocation()->getType();
        }
        if(isset($metaLocation)){
            $bucketDetailInfo->getMetaLocation()->getLocation();
        }

    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

//################################################################################

/**
 * Check whether a bucket exists.
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 */
function doesBucketExist($oosClient, $bucket)
{
    try {
        $res = $oosClient->doesBucketExist($bucket);
    } catch (OosException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    if ($res === true) {
        print(__FUNCTION__ . ": OK" . "\n");
    } else {
        print(__FUNCTION__ . ": FAILED" . "\n");
    }
}

/**
 * Check whether a bucket exists.
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 */
function headBucket($oosClient, $bucket)
{
    try {
        $bExists = $oosClient->headBucket($bucket);

    } catch (OosException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    if ($bExists === true) {
        print($bucket . ": exists" . "\n");
    } else {
        print($bucket . ": not exists" . "\n");
    }
}
/**
 * Delete a bucket. If the bucket is not empty, the deletion fails.
 * A bucket which is not empty indicates that it does not contain any objects or parts that are not completely uploaded during multipart upload
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket Name of the bucket to delete
 * @return null
 */
function deleteBucket($oosClient, $bucket)
{
    try {
        $options = array();

        $oosClient->deleteBucket($bucket,$options);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Set bucket ACL
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function putBucketAcl($oosClient, $bucket)
{
    $acl = OosClient::OOS_ACL_TYPE_PUBLIC_READ_WRITE;
    try {
        $oosClient->putBucketAcl($bucket, $acl);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Get bucket ACL
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getBucketAcl($oosClient, $bucket)
{
    try {
        $options = array();

        $bucketAcl = $oosClient->getBucketAcl($bucket,$options);
        $granteeList = $bucketAcl->getGranteeList();
        foreach ($granteeList as $grantee){
            $grantee->getPermission();
        }
        $bucketAcl->getOwner()->setDisplayName();
        $bucketAcl->getOwner()->getId();

    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}
/**
 * List bucket objects
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function listObjects($oosClient,$bucket)
{
    $listObjectsResult = null;
    try {
        $options = array();
        $options["max-keys"] = 40;
        $options["prefix"] = "doc/";
        $options["delimiter"] = "/";
        $options["marker"] = "doc/F";

        $listObjectInfo = $oosClient->listObjects($bucket,$options);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    $bucketName = $listObjectInfo->getBucketName();
    $objectList = $listObjectInfo->getObjectList(); // object list
    $prefixList = $listObjectInfo->getPrefixList(); // directory list
    if (!empty($objectList)) {
        print("objectList:\n");
        foreach ($objectList as $objectInfo) {
            print($objectInfo->getKey() . "\n");
        }
    }
    if (!empty($prefixList)) {
        print("prefixList: \n");
        foreach ($prefixList as $prefixInfo) {
            print($prefixInfo->getPrefix() . "\n");
        }
    }
}


/**
 * Get ongoing multipart uploads
 *
 * @param $oosClient OosClient
 * @param $bucket   string
 */
function listMultipartUploads($oosClient, $bucket)
{
    $options = array(
        'delimiter' => '/',
        'max-uploads' => 100,
        // 'key-marker' => '',
        'prefix' => '',
        'upload-id-marker' => ''
    );
    try {
        $listMultipartUploadInfo = $oosClient->listMultipartUploads($bucket, $options);
        printf(__FUNCTION__ . ": listMultipartUploads OK\n");
        $uploads = $listUploadInfo = $listMultipartUploadInfo->getUploads();
        foreach ($uploads as $upload){
            printf("\n key: " . $upload->getKey());
            $owner = $upload->getOwner();
            $initiator = $upload->getInitiator();
            if(isset($owner)){
                printf("\n ownerId: " . $owner->getId());
                $owner->getDisplayName();
            }
            if(isset($owner)){
                printf("\n ownerId: " . $initiator->getId());
                $initiator->getDisplayName();
            }
        }
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    catch (Exception $e) {
        print($e->getMessage());
        return;
    }

    printf("\n");
    var_dump($listUploadInfo);
}

//******************************* For complete usage, see the following functions ****************************************************
//1  5.0的资源池创建bucket#############################
// Bucket	Put Bucket	PUT	创建一个新的bucket
//createBucket($oosClient, $bucket);

//1.1 6.0的资源池创建bucket #############################
// Bucket	Put Bucket	PUT	创建一个新的bucket
//createBucketWithLocation($oosClient, $bucket);

//2 #############################
//Bucket	Get Bucket location	GET 	获取bucket的索引位置和数据位置
//getBucketLocation($oosClient, $bucket);

//3 #############################
//Bucket	Get bucket acl	GET 	获取bucket的ACL信息
//getBucketAcl($oosClient, $bucket);

//4 #############################
//Bucket	GET Bucket (List Objects)	GET 	返回bucket中部分或者全部（最多1000）的object信息。
//listObjects($oosClient,$bucket);

//5	Bucket	Delete bucket	DELETE	执行删除bucket的操作
//deleteBucket($oosClient, $bucket);

//15	Bucket	Head Bucket	Head	判断bucket是否存在，而且用户是否有权限访问。如果bucket存在，
//而且用户有权限访问时，此操作返回200 OK。否则，返回404 不存在，或者403 没有权限
//headBucket($oosClient, $bucket);

listMultipartUploads($oosClient, $bucket);
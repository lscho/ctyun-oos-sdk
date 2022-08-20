<?php
require_once __DIR__ . '/Common.php';
header("Content-type: text/html; charset=utf-8");

use OOS\OosClient;
use OOS\Core\OosException;
use OOS\Model\DataLocation;
use OOS\Core\OosUtil;
use OOS\Model\GetObjectInfo;
use OOS\Model\ObjectInfo;

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
function createObjectDir($oosClient, $bucket)
{
    try {
        $oosClient->createObjectDir($bucket, "dir");
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Upload in-memory data to oss
 *
 * Simple upload---upload specified in-memory data to an OOS object
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 * @note 该方法资源池5和资源池6都可以用
 */
function putObject($oosClient, $bucket)
{
    $object = "Service.php";
    $content = file_get_contents("Service.php");
    $options = array();
    $xHeaders = array();

    $xHeaders["Cache-Control"] = "yes";
    $xHeaders["x-amz-meta-aaa"] = "aaa";
    $xHeaders["x-amz-storage-class"] = "STANDARD";
    $xHeaders[OosClient::OOS_CONTENT_MD5] = base64_encode(md5($content, true));

    $options[OosUtil::OOS_HEADERS] = $xHeaders;
    try {
        $oosClient->putObject($bucket, $object, $content, $options,null);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Upload in-memory data to oss
 *
 * Simple upload---upload specified in-memory data to an OOS object
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 * @note 该例子只能在资源池6使用
 */
function putObjectToLcation($oosClient, $bucket)
{
    $object = "object.php";
    $content = file_get_contents(__FILE__);
    $options = array();
    $xHeaders = array();
    $xHeaders["Cache-Control"] = "yes";
    $xHeaders["x-amz-meta-aaa"] = "aaa";
    $xHeaders["x-amz-storage-class"] = "STANDARD";

    $options[OosUtil::OOS_HEADERS] = $xHeaders;
    try {
        $type = "Specified";
        $locationList = array();
        $locationList["ShenYang"] = "ShenYang";
        $locationList["ChengDu"] = "ChengDu";
        $scheduleStrategy = "NotAllowed";
        $dataLocation =  new DataLocation($type,$locationList,$scheduleStrategy);

        $oosClient->putObject($bucket, $object, $content, $options,$dataLocation);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}
/**
 * Upload in-memory data to oss
 *
 * Simple upload---upload String  data to an OOS object
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function putObjectStringContent($oosClient, $bucket)
{
    $objectName = "test1.txt";
    $objectContent = "hello world!";
    $options = array();

    try {
        $oosClient->putObject($bucket, $objectName, $objectContent, $options);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Uploads a local file to OOS
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function uploadFile($oosClient, $bucket)
{
    $object = "oss-php-sdk-test/upload-test-object-name.txt";
    $filePath = __FILE__;
    $options = array();

    try {
        $oosClient->uploadFile($bucket, $object, $filePath, $options);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Lists all folders and files under the bucket. Use nextMarker repeatedly to get all objects.
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @throws
 * @return null
 */
function listAllObjects($oosClient, $bucket)
{
    // Create dir/obj 'folder' and put some files into it.
    for ($i = 0; $i < 100; $i += 1) {
        $oosClient->putObject($bucket, "dir/obj" . strval($i), "hi");
        $oosClient->createObjectDir($bucket, "dir/obj" . strval($i));
    }

    $prefix = 'dir/';
    $delimiter = '/';
    $nextMarker = '';
    $maxkeys = 30;

    while (true) {
        $options = array(
            'delimiter' => $delimiter,
            'prefix' => $prefix,
            'max-keys' => $maxkeys,
            'marker' => $nextMarker,
        );
        var_dump($options);
        try {
            $listObjectInfo = $oosClient->listObjects($bucket, $options);
        } catch (OosException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        // Get the nextMarker, and it would be used as the next call's marker parameter to resume from the last call
        $nextMarker = $listObjectInfo->getNextMarker();
        $listObject = $listObjectInfo->getObjectList();
        $listPrefix = $listObjectInfo->getPrefixList();

        var_dump(count($listObject));
        var_dump(count($listPrefix));
        if ($nextMarker === '') {
            break;
        }
    }
}

/**
 * Get the content of an object.
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return GetObjectInfo
 */
function getObject($oosClient, $bucket)
{
    $object = "iloveyou.vbs";
    $options = array();
    $xHeaders = array();
    $xHeaders["Range"] = "bytes=1-100";
    $options[OosUtil::OOS_HEADERS] = $xHeaders;
    try {
        $objectInfo = $oosClient->getObject($bucket, $object, $options);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    $objectInfo->getETag();
    $objectInfo->getMetaLocation();
    return  $objectInfo;
}


/**
 * Get_object_to_local_file
 *
 * Get object
 * Download object to a specified file.
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getObjectToLocalFile($oosClient, $bucket)
{
    $object = "oss-php-sdk-test/upload-test-object-name.txt";
    $localfile = "upload-test-object-name.txt";
    $options = array(
        OosClient::OOS_FILE_DOWNLOAD => $localfile,
    );

    try {
        $objectInfo = $oosClient->getObject($bucket, $object, $options);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }

    if (file_exists($localfile)) {
        unlink($localfile);
    }
}

/**
 * Copy object
 * When the source object is same as the target one, copy operation will just update the metadata.
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function copyObject($oosClient, $bucket)
{
    $fromBucket = $bucket;
    $fromObject = "v21.txt";
    $toBucket = $bucket;
    $toObject = $fromObject . '.copy1';

    $options = array();

    try {
        $type = "Specified";
        $locationList = array();
        $locationList["ShenYang"] = "ShenYang";
        $locationList["ChengDu"] = "ChengDu";
        $scheduleStrategy = "NotAllowed";
        $dataLocation =  new DataLocation($type,$locationList,$scheduleStrategy);

        $copyPartInfo= $oosClient->copyObject($fromBucket, $fromObject, $toBucket, $toObject, $options,$dataLocation);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    $copyPartInfo->getETag();
}

/**
 * Get object meta, that is, getObjectMeta
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function headObject($oosClient, $bucket)
{
    $object = "object.php";
    try {
        $objectInfo = $oosClient->headObject($bucket, $object);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print($objectInfo->getETag());
}

/**
 * Delete an object
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteObject($oosClient, $bucket)
{
    $object = "oss-php-sdk-test/upload-test-object-name.txt";
    try {
        $oosClient->deleteObject($bucket, $object);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Delete multiple objects in batch
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteObjects($oosClient, $bucket)
{
    $objects = array();
    $objects[] = "mpu/multipart-test.txt";
    $objects[] = "mpu/multipart-test.txt.copied";
    $options = array();
    $options['quiet'] = false;
    try {
        $oosClient->deleteObjects($bucket, $objects,$options);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Check whether an object exists
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function doesObjectExist($oosClient, $bucket)
{
    $object = "oss-php-sdk-test/upload-test-object-name.txt";
    try {
        $exist = $oosClient->doesObjectExist($bucket, $object);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    var_dump($exist);
}

function getObjectToAppend($oosClient, $bucket)
{
    putObject($oosClient, $bucket);

    $appenedFileName = date('Ymdhis-').rand(10000000, 99999999) . ".log";

    file_put_contents($appenedFileName,"My key is K123456",FILE_APPEND);
    file_put_contents($appenedFileName,"Any Data",FILE_APPEND);


    $objectInfo = getObject($oosClient,$bucket);
    file_put_contents($appenedFileName,var_export($objectInfo->getContent(),true),FILE_APPEND);

    return $appenedFileName;

}


//******************************* For complete usage, see the following functions ****************************************************
//1	Object	Put Object	PUT	Put操作用来向指定bucket中添加一个对象
// putObject($oosClient, $bucket);
//putObjectToLcation($oosClient, $bucket);
//2 Object	Get Object	PUT	GET操作用来检索在OOS中的对象信息，执行GET操作，用户必须对object所在的bucket有读权限。
getObject($oosClient, $bucket);

//3	Object	Delete Object	Delete	Delete操作移除指定的对象
//deleteObject($oosClient, $bucket);

//4	Object	PUT Object - Copy	PUT	通过PUT操作创建一个存储在OOS里的对象的拷贝
//copyObject($oosClient, $bucket);

//12 Object	Copy Part	PUT	批量删除Object功能支持用一个HTTP请求删除一个bucket中的多个object。
//deleteObjects($oosClient, $bucket);

//14	Object	POST Object	Post	POST操作使用HTML表单将对象上传到指定的Bucket。
//deleteObjects($oosClient, $bucket);

//17	Object	HEAD Object	Head 操作用于获取对象的元数据信息，而不返回数据本身。
//headObject($oosClient, $bucket);

//putObjectStringContent($oosClient, $bucket);

//getObjectToAppend($oosClient,$bucket);


<?php
require_once __DIR__ . '/Common.php';

use OOS\OosClient;
use OOS\Core\OosUtil;
use OOS\Core\OosException;
use OOS\Model\InitiateMultipartUploadInfo;
use OOS\Model\PartInfo;
use OOS\Model\CompleteMultipartUploadInfo;

$bucket = Common::getBucketName();
$oosClient = Common::getOosClient();
if (is_null($oosClient)) exit(1);

//******************************* Simple usage ***************************************************************
/**
 * Upload files using multipart upload
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function multiuploadFile($oosClient, $bucket)
{
    $object = "aliyun-oss-android-sdk-master.zip";
    //$file = __FILE__;
    $file = "D:\\Software\\aliyun-oss-android-sdk-master.zip";
    $options = array();

    try {
        $completeMultipartUploadInfo = $oosClient->multiuploadFile($bucket, $object, $file, $options);
        print("\n key:" . $completeMultipartUploadInfo->getKey() . "\n");
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }

    catch (Exception $e) {
        print($e->getMessage());
        return;
    }
    print(__FUNCTION__ . ":  OK" . "\n");
}

/**
 * Use basic multipart upload for file upload.
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @throws OosException
 */
function putObjectByRawApis($oosClient, $bucket)
{
    $object = "test/multipart-test.txt";
    /**
     *  step 1. Initialize a block upload event, that is, a multipart upload process to get an upload id
     */
    try {
        $uploadId = $oosClient->initiateMultipartUpload($bucket, $object);
    } catch (OosException $e) {
        $e->printException(initiateMultipartUpload);
        return;
    }
    print(__FUNCTION__ . ": initiateMultipartUpload OK" . "\n");
    /*
     * step 2. Upload parts
     */
    $partSize = 10 * 1024 * 1024;
    $uploadFile = __FILE__;
    $uploadFileSize = filesize($uploadFile);
    $pieces = $oosClient->generateMultiuploadParts($uploadFileSize, $partSize);
    $responseUploadPart = array();
    $uploadPosition = 0;
    $isCheckMd5 = true;
    foreach ($pieces as $i => $piece) {
        $fromPos = $uploadPosition + (integer)$piece[$oosClient::OOS_SEEK_TO];
        $toPos = (integer)$piece[$oosClient::OOS_LENGTH] + $fromPos - 1;
        $upOptions = array(
            $oosClient::OOS_FILE_UPLOAD => $uploadFile,
            $oosClient::OOS_PART_NUM => ($i + 1),
            $oosClient::OOS_SEEK_TO => $fromPos,
            $oosClient::OOS_LENGTH => $toPos - $fromPos + 1,
            $oosClient::OOS_CHECK_MD5 => $isCheckMd5,
        );
        if ($isCheckMd5) {
            $contentMd5 = OosUtil::getMd5SumForFile($uploadFile, $fromPos, $toPos);
            $upOptions[$oosClient::OOS_CONTENT_MD5] = $contentMd5;
        }
        //2. Upload each part to OOS
        try {
            $responseUploadPart[] = $oosClient->uploadPart($bucket, $object, $uploadId, $upOptions);
        } catch (OosException $e) {
            $e->printException(__FUNCTION__ . ": initiateMultipartUpload, uploadPart - part#{$i} FAILED\n");
            return;
        }
        printf(__FUNCTION__ . ": initiateMultipartUpload, uploadPart - part#{$i} OK\n");
    }
    $uploadParts = array();
    foreach ($responseUploadPart as $i => $eTag) {
        $uploadParts[] = array(
            'PartNumber' => ($i + 1),
            'ETag' => $eTag,
        );
    }
    /**
     * step 3. Complete the upload
     */
    try {
        $oosClient->completeMultipartUpload($bucket, $object, $uploadId, $uploadParts);
    } catch (OosException $e) {
        printf(__FUNCTION__ . ": completeMultipartUpload FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    printf(__FUNCTION__ . ": completeMultipartUpload OK\n");
}

/**
 * Upload by directories
 *
 * @param OosClient $oosClient OosClient
 * @param string $bucket bucket name
 *
 */
function uploadDir($oosClient, $bucket)
{
    $localDirectory = ".";
    $prefix = "samples/codes";
    try {
        $oosClient->uploadDir($bucket, $prefix, $localDirectory);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    printf(__FUNCTION__ . ": completeMultipartUpload OK\n");
}

/**
 * Use basic initiate Multipart Upload  for file upload.
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @throws
 */
function initiateMultipartUpload($oosClient, $bucket)
{
    $object = "android-sample.rar";
    $options = array();

    try {
        $uploadInfo = $oosClient->initiateMultipartUpload($bucket, $object, $options);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ":  OK" . "\n");
    print("uploadId=" . $uploadInfo->getUploadId() . " . \n");
}

/**
 * Use basic initiate Multipart Upload  for file upload.
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @param string $uploadId upload id (initiated by initiateMultipartUpload)
 * @throws
 */
function uploadPart($oosClient, $bucket,$uploadId)
{
    $object = "android-sample.rar";
    $file = "D:\\ZDX_OOS_SDK_6.X\\android-sample.rar";
    $options = array();
    $options["fileUpload"] = OosUtil::encodePath($file);
    $options["partNumber"] = 1;

    try {
        $etag = $oosClient->uploadPart($bucket,$object,$uploadId,$options);
        print("\n===etag: " . (string)$etag . "\n");
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ":  OK" . "\n");
}

/**
 * complete Multipart Upload  for file upload.
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @param string $uploadId upload id (initiated by initiateMultipartUpload)
 * @throws
 */
function completeMultipartUpload($oosClient, $bucket,$uploadId)
{
    $object = "android-sample.rar";
    $uploadParts = array();
    $uploadParts[] = array(
        'PartNumber' => 1,
        'ETag' => "c380eb03049998280895078d570cb944"
    );
    /*
    $uploadParts[] = array(
        'PartNumber' => 2,
        'ETag' => "306010df87ac70109e9aec2048398440"
    );
    */
    try {
        $completeMultipartUploadInfo = $oosClient->completeMultipartUpload($bucket,$object,$uploadId,$uploadParts);
        $completeMultipartUploadInfo->getEtag();
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    catch (Exception $e) {
        print($e->getMessage());
        return;
    }
    print(__FUNCTION__ . ":  OK" . "\n");
    $completeMultipartUploadInfo->getKey();
}

/**
 * abort Multipart Upload  for file upload.
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @param string $uploadId upload id (initiated by initiateMultipartUpload)
 * @throws
 */
function abortMultipartUpload($oosClient, $bucket,$uploadId)
{
    $object = "seL4.zip";
    try {
        $oosClient->abortMultipartUpload($bucket,$object,$uploadId);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ":  OK" . "\n");
    print("uploadId=" . "$uploadId\n");
}


/**
 * copy ObjectPart.
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $fromBucket bucket name
 * @param string $fromObject from object key
 * @param string $toBucket to bucket name
 * @param string $toObject to object key
 * @param string $toPartNumber to PartNumber
 * @param string $toUploadId to Upload Id
 * @throws
 */
function copyObjectPart($oosClient, $fromBucket, $fromObject, $toBucket, $toObject,$toPartNumber,$toUploadId)
{
    $options = array();
    try {
        $copyPartInfo = $oosClient->copyObjectPart($fromBucket, $fromObject, $toBucket, $toObject,
            $toPartNumber,$toUploadId,$options);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    return $copyPartInfo->getETag();
}

/**
 * copy ObjectPart.
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @param string $object  object key
 * @param string $uploadId uploadId
 * @throws
 */
function listParts($oosClient, $bucket,$object,$uploadId)
{
    $options = array(
        'max-parts' => 1000,
        'part-number-marker' => 2,
    );
    try {
        $listPartsInfo = $oosClient->listParts($bucket, $object, $uploadId, $options);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    printf(__FUNCTION__ . ": listParts OK\n");
    $listPart = $listPartsInfo->getListPart();
    foreach ($listPart as $part){
        $part->getETag();
        $part->getPartNumber();
    }
    var_dump($listPart);
}

/**
 * copy ObjectPart.
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @throws
 */
function testCopyPart($oosClient,$bucket)
{
    try {
        $toObject = "mpu/multipart-test.txt";
        $copiedObject = "mpu/multipart-test.txt.copied";
        $oosClient->putObject($bucket, $copiedObject, file_get_contents(__FILE__));
        /**
         *  step 1. 初始化一个分块上传事件, 也就是初始化上传Multipart, 获取upload id
         */
        $uploadInfo = $oosClient->initiateMultipartUpload($bucket, $toObject);
        $upload_id = $uploadInfo->getUploadId();
        /**
         * step 2. uploadPartCopy
         */
        $options = array();
        $copyId = 1;
        $eTag = copyObjectPart($oosClient,$bucket, $copiedObject, $bucket,
            $toObject, $copyId, $upload_id,$options);

        $upload_parts[] = array(
            'PartNumber' => $copyId,
            'ETag' => $eTag,
        );

        /**
         * step 3.
         */
        $oosClient->completeMultipartUpload($bucket, $toObject, $upload_id, $upload_parts);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }

    print(__FUNCTION__ . ":  OK" . "\n");
}

//******************************* For complete usage, see the following functions ****************************************************
//################完整的分片上传例子################################
multiuploadFile($oosClient, $bucket);

//5	Object	Initial Multipart Upload	Post	本接口初始化一个分片上传（Multipart Upload）操作，
//并返回一个上传ID，此ID用来将此次分片上传操作中上传的所有片段合并成一个对象。用户在执行每一次子上传请求（见Upload Part）
//时都应该指定该ID。用户也可以在表示整个分片上传完成的最后一个请求中指定该ID。
# initiateMultipartUpload($oosClient, $bucket);

//6	Object	Upload Part	Post	在上传任何一个分片之前，必须执行Initial Multipart Upload操作来初始化分片上传操作，
//初始化成功后，OOS会返回一个上传ID，这是一个唯一的标识，用户必须在调用Upload Part接口时加入该ID
//uploadPart($oosClient, $bucket,1564731298450310063);

//7	Object	Complete Multipart Upload	Post	该接口通过合并之前的上传片段来完成一次分片上传过程。
//初始化成功后，OOS会返回一个上传ID，这是一个唯一的标识，用户必须在调用Upload Part接口时加入该ID
//completeMultipartUpload($oosClient, $bucket,1564731298450310063);

//8	Object	Abort Multipart Upload	Post	该接口用于终止一次分片上传操作。
//abortMultipartUpload($oosClient, $bucket,1564731298450310063);

//9	Object	List Part	GET	列出一次分片上传过程中已经上传完成的所有片段
//listMultipartUploads($oosClient, $bucket);

//10	Object	Copy Part	PUT	可以将已经存在的object作为分段上传的片段，拷贝生成一个新的片段
//testCopyPart($oosClient, $bucket);

//listParts($oosClient, $bucket,"httpd-2.4.38.tar.bz2","1564731298450310063");



<?php
require_once __DIR__ . '/Common.php';

use OOS\OosClient;
use OOS\Core\OosException;

$oosClient = Common::getOosClient();
if (is_null($oosClient)) exit(1);
$bucket = Common::getBucketName();

/**
 * Set bucket Policy
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function putBucketPolicy($oosClient, $bucket)
{
    $options = array();

    $textPolicy = <<< BPLY
            {
                    'Version': '2012-10-17',
                    'Id': 'http referer policy example',
                    'Statement': [
                        {
                            'Sid': 'Allow get requests referred by www.mysite.com ',
                            'Effect': 'Allow',
                            'Principal': {
                                'AWS': [
                                    '*'
                                ]
                        },
                            'Action': 's3:*',
                            'Resource': 'arn:aws:s3:::testphp2/*',
                            'Condition': {
                                'StringLike': {
                                    'aws:Referer': [
                                        'http://www.mysite.com/*'
                                    ]
                                }
                            }
                        }
                    ]
                }
    BPLY;

    try {
        $oosClient->putBucketPolicy($bucket, $textPolicy,$options);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get and print the Policy configuration of a bucket
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getBucketPolicy($oosClient, $bucket)
{
    $policyJson = null;
    try {
        $policyJson = $oosClient->getBucketPolicy($bucket);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print("Policy:" . $policyJson . "\n");
}

/**
 * Delete all Policy configuraiton of a bucket
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteBucketPolicy($oosClient, $bucket)
{
    try {
        $oosClient->deleteBucketPolicy($bucket);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}



//******************************* For complete usage, see the following functions  *****************************************************
//6	Bucket	Put Bucket Policy	PUT	进行添加或修改policy的操作。如果bucket已经存在了Policy，此操作会替换原有Policy。
//只有bucket的owner才能执行此操作，否则会返回403 AccessDenied错误。
//putBucketPolicy($oosClient, $bucket);

//7	Bucket	Get Bucket Policy	GET 	获得指定bucket的policy。只有bucket的owner才能执行此操作，否则会返回403
//AccessDenied错误。如果bucket没有policy，返回404，NoSuchPolicy错误。
//getBucketPolicy($oosClient, $bucket);

//8	Bucket	Delete bucket Policy	PUT	删除指定bucket的policy。只有bucket的owner才能执行此操作，否则会返回403
// AccessDenied错误。如果bucket没有policy，返回204 NoContent。
//deleteBucketPolicy($oosClient, $bucket);



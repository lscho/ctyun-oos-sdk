<?php
require_once __DIR__ . '/Common.php';

use OOS\OosClient;
use OOS\Core\OosException;
use OOS\Model\LifecycleAction;
use OOS\Model\LifecycleConfig;
use OOS\Model\LifecycleRule;

$bucket = Common::getBucketName();
$oosClient = Common::getOosClient();
if (is_null($oosClient)) exit(1);

/**
 * Set bucket lifecycle configuration
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws
 */
function putBucketLifecycle($oosClient, $bucket)
{
    $lifecycleConfig = new LifecycleConfig();
    $actions = array();
    $actions[] = new LifecycleAction(OosClient::OOS_LIFECYCLE_EXPIRATION, OosClient::OOS_LIFECYCLE_TIMING_DAYS, 3);
    $lifecycleRule = new LifecycleRule("delete obsoleted files", "obsoleted/", "Enabled", $actions);
    $lifecycleConfig->addRule($lifecycleRule);
    $actions = array();
    $actions[] = new LifecycleAction(OosClient::OOS_LIFECYCLE_EXPIRATION, OosClient::OOS_LIFECYCLE_TIMING_DATE, '2022-10-12T00:00:00.000Z');
    $lifecycleRule = new LifecycleRule("delete temporary files", "temporary/", "Enabled", $actions);
    $lifecycleConfig->addRule($lifecycleRule);
    try {
        $oosClient->putBucketLifecycle($bucket, $lifecycleConfig);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get bucket lifecycle configuration
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getBucketLifecycle($oosClient, $bucket)
{
    $lifecycleConfig = null;
    try {
        $lifecycleConfig = $oosClient->getBucketLifecycle($bucket);
        $rules = $lifecycleConfig->getRules();
        foreach ($rules as $rule){
            $rule->getId();
            $actions = $rule->getActions();
            foreach ($actions as $action){
                $action->getAction();
                $action->getTimeSpec();
                $action->getTimeValue();
            }
        }
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print($lifecycleConfig->serializeToXml() . "\n");
}

/**
 * Delete bucket lifecycle configuration
 *
 * @param OosClient $oosClient OosClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteBucketLifecycle($oosClient, $bucket)
{
    try {
        $oosClient->deleteBucketLifecycle($bucket);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

//***************************** For complete usage, see the following functions  ***********************************************

//16	Bucket	Put Bucket Lifecycle	Head	?????????????????????????????????bucket????????????????????????
//putBucketLifecycle($oosClient, $bucket);

//17	Bucket	Get Bucket Lifecycle	GET 	???????????????bucket????????????
getBucketLifecycle($oosClient, $bucket);

//18	Bucket	Delete Bucket Lifecycle	DELETE	??????????????????????????????bucket???????????????
//OOS??????????????????bucket?????????????????????????????????
//deleteBucketLifecycle($oosClient, $bucket);


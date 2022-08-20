<?php
require_once __DIR__ . '/Common.php';

use OOS\OosClient;
use OOS\Core\OosException;
use OOS\Model\KeyInfo;

use OOS\Model\DeleteUpdateAccessKeyInfo;

$oosClient = Common::getOosIamClient();
if (is_null($oosClient)) exit(1);

/**
 * create AccessKey
 * @param OOSClient $oosClient
 * @throws
 * @return null
 */
function createAccessKey($oosClient)
{
    try {
        $options = array();
        $options["bucket"] = "";

        $keyInfo = $oosClient->CreateAccessKey($options);
        $keyInfo->getAccessKeyId();
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * delete AccessKey
 * @param OOSClient $oosClient
 * @param string $accessKeyId
 * @throws
 * @return null
 */
function deleteAccessKey($oosClient,$accessKeyId)
{
    try {
        $options = array();

        $data = $oosClient->DeleteAccessKey($accessKeyId);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }

    print(__FUNCTION__ . ": OK" . "\n");
    print("\n The delete accessKey request id is :" . $data->getRequestId() . "\n");
}

/**
 * update AccessKey
 * @param OOSClient $oosClient
 * @param string $accessKeyId
 * @param string $status
 * @param string $isPrimary
 * @throws
 * @return null
 */
function updateAccessKey($oosClient,$accessKeyId,$status,$isPrimary)
{
    try {
        $options = array();
        $data = $oosClient->UpdateAccessKey($accessKeyId,$status,$isPrimary);
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    print("\n The update accessKey request id is :" . $data->getRequestId() . "\n");
}

/**
 * update AccessKey
 * @param OOSClient $oosClient
 * @param string $MaxItems
 * @param string $Marker
 * @throws
 * @return null
 */
function listAccessKey($oosClient,$MaxItems,$Marker)
{
    $options = array();
    $options["bucket"] = "";

    $keyList =  array();
    try {
        if($MaxItems<=0)
            $MaxItems = 100;

        $keyList = $oosClient->ListAccessKey($MaxItems,$Marker,$options);
        $userName = $keyList->getUserName();

        foreach ($keyList->getKeyList() as $keyInfo){
            $keyInfo->getUserName();
            $keyInfo->getStatus();
        }
    } catch (OosException $e) {
        $e->printException(__FUNCTION__);
        return;
    }

    foreach ($keyList->getKeyList() as $keyOne) {
        print("ListAccessKey" . "\t accessKeyId:" . $keyOne->getAccessKeyId()
            . "\t status:" . $keyOne->getStatus()
            . "\t isPrimary:" . $keyOne->getIsPrimary()
            . "\n");
    }

    $keyList->getIsTruncated();
    $keyList->getMarker();
    print(__FUNCTION__ . ": OK" . "\n");

}

//******************************* For complete usage, see the following functions ****************************************************
//1 #############################
// 1 创建一对普通的AccessKey和SecretKey，默认的状态是Active
//createAccessKey($oosClient);

//2 #############################
//DeleteAccessKey	POST	删除一对普通的AccessKey和SecretKey
//deleteAccessKey($oosClient,"656391ac8f0ef1067ce7");

//3 #############################
//UpdateAccessKey	POST	更新普通的AccessKey的状态，或将普通key设置成为主key，反之亦然
////accessKeyId string, bActive, isPrimary bool
//updateAccessKey($oosClient,"6748d311decc463879b4","true","false");

//4 #############################
//ListAccessKey	POST	列出账号下的主key和普通key
listAccessKey($oosClient,50,"");



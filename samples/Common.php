<?php

if (is_file(__DIR__ . '/../autoload.php')) {
    require_once __DIR__ . '/../autoload.php';
}
if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}


use OOS\OosClient;
use OOS\Core\OosException;
use OOS\Config\Config;

/**
 * Class Common
 *
 * The Common class for 【Samples/*.php】 used to obtain OosClient instance and other common functions
 */
class Common
{
    const endpoint = Config::OOS_ENDPOINT;
    const endpointIam = Config::OOS_ENDPOINT_ACCESS;
    const accessKeyId = Config::OOS_ACCESS_ID;
    const accessKeySecret = Config::OOS_ACCESS_KEY;
    const bucket = Config::OOS_TEST_BUCKET;

    public static function getOosClient()
    {
        try {
            $oosClient = new OosClient(self::accessKeyId, self::accessKeySecret, self::endpoint, false);
        } catch (OosException $e) {
            printf(__FUNCTION__ . "creating OosClient instance: FAILED\n");
            printf($e->getMessage() . "\n");
            return null;
        }
        return $oosClient;
    }

    public static function getOosIamClient()
    {
        try {
            $oosClient = new OosClient(self::accessKeyId, self::accessKeySecret,
                self::endpointIam, false);
        } catch (OosException $e) {
            printf(__FUNCTION__ . "creating OosIamClient instance: FAILED\n");
            printf($e->getMessage() . "\n");
            return null;
        }
        return $oosClient;
    }

    public static function getBucketName()
    {
        return self::bucket;
    }
}


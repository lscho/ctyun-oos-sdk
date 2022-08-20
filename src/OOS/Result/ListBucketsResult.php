<?php

namespace OOS\Result;

use OOS\Model\BucketInfo;
use OOS\Model\BucketListInfo;
use OOS\Model\Owner;

/**
 * Class ListBucketsResult
 *
 * @package OOS\Result
 */
class ListBucketsResult extends Result
{
    /**
     * @return BucketListInfo
     */
    protected function parseDataFromResponse()
    {
        $strXml = $this->rawResponse->body;
        if (empty($strXml)) {
            throw new OosException("body is null");
        }
        $xml = simplexml_load_string($strXml);

        $bucketListInfo= new BucketListInfo();
        $bucketList = array();
        if (isset($xml->Buckets) && isset($xml->Buckets->Bucket)) {
            foreach ($xml->Buckets->Bucket as $bucket) {
                $bucketInfo = new BucketInfo(strval($bucket->Location),
                    strval($bucket->Name),
                    strval($bucket->CreationDate));
                $bucketList[] = $bucketInfo;
            }
        }
        $id = "";
        $displayName = "";
        if (isset($xml->Owner) && isset($xml->Owner->ID)) {
            $id = strval($xml->Owner->ID);
        }
        if (isset($xml->Owner) && isset($xml->Owner->DisplayName)) {
            $displayName = strval($xml->Owner->DisplayName);
        }

        $owner = new Owner($id,$displayName);

        $bucketListInfo->setBucketList($bucketList);
        $bucketListInfo->setOwner($owner);

        return $bucketListInfo;
    }
}
<?php

namespace OOS\Model;
use OOS\Model\Owner;
use OOS\Model\Grantee;

/**
 * Class BucketAcl
 * @package OOS\Model
 */
class BucketAcl
{
    public function __construct($owner,$granteeList)
    {
        $this->owner = $owner;
        $this->granteeList = $granteeList;
    }

    /**
     *
     * @return Owner
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     *
     * @return Grantee[]
     */
    public function getGranteeList()
    {
        return $this->granteeList;
    }

    private $owner;
    private $granteeList;
}



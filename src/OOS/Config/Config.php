<?php

/**
 * Class Config
 *
 * Make configurations required by the sample.
 * Users can run RunAll.php which runs all the samples after configuring Endpoint, AccessId, and AccessKey.
 */

namespace OOS\Config;

final class Config
{
    /*
    // 5.0
    const OOS_ACCESS_ID = 'e9*d0c63*a39ac986057';                           //your id
    const OOS_ACCESS_KEY = 'dcd7ec51fec2eba2f91e47c7e140ac65*39a7b21';      //your key
    const OOS_ENDPOINT = 'oos-js.ctyunapi.cn';                              //your domain
    const OOS_ENDPOINT_ACCESS = 'oos-cn-iam.ctyunapi.cn';                   //your domain for iam
    const OOS_TEST_BUCKET = 'testphp8';                                     //your bucket
    const SIGNER_USING_V4 = false;                                          //是否使用V4签名、false为V2签名，5的资源池只能为false

     */
    /**/
                                                                             // 6.0
    const OOS_ACCESS_ID        = 'fadd131fe11bd2b94666';                     //your id
    const OOS_ACCESS_KEY       = '6eb36ff77239ba025332b12db49f572852c31ff4'; //your key
    const OOS_ENDPOINT         = 'oos-cn.ctyunapi.cn';                       //your domain
    const OOS_ENDPOINT_ACCESS  = 'oos-cn-iam.ctyunapi.cn';                   //your domain for iam
    const OOS_TEST_BUCKET      = 'zhouyur';                                  //your bucket
    const SIGNER_USING_V4      = true;                                       //是否使用V4签名，false为使用V2签名
    const SIGNER_USING_PAYLOAD = true;                                       //负载是否参与签名
}
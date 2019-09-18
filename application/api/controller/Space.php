<?php

namespace app\api\controller;

use app\helps\Oss;

/**
 * Class Space
 * @package app\api\controller
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))
 */
class Space extends Base
{
    public function oss()
    {
        $oss = new Oss();
        $data = $oss->getCredentials();
        $data['regionId'] = config('aliyun.oss.regionId');
        $data['bucket'] = config('aliyun.oss.bucket');
        $data['endpoint'] = config('aliyun.oss.endpoint');
        return $this->succ($data);
    }
}
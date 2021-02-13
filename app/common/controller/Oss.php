<?php


namespace app\general\controller;


use aliyun\sts\StsExtend;
use app\BaseController;
use think\Cache;

class Oss extends BaseController
{
    public function getSts()
    {
        $res = Cache::get('Credentials');
        if (!$res || empty($res))
        {
            $model = new StsExtend();
            $res = $model->getSts();
            $credentials = $res['Credentials'];
            $expired_time = $credentials['Expiration'];
            Cache::set('Credentials', json_encode($credentials), strtotime($expired_time) - time() - 5 * 60);
            $res = $credentials;
        } else {
            $res = json_decode($res, true);
        }
        $this->sendResponse(0, $res);
    }

}
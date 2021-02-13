<?php
/**
 * Created by PhpStorm.
 * User: LucasXu
 * Date: 2020/3/6
 * Time: 18:41
 * Class AliCloudImm
 * @package general
 */

namespace app\common\controller;

use aliyun\imm\ImmExtend;
use app\BaseController;

class AliCloudImm extends BaseController
{
    /**
     * @param $path
     * @param $target
     * @throws \AlibabaCloud\Client\Exception\ClientException
     * @throws \AlibabaCloud\Client\Exception\ServerException
     */
    public function convertFileToImg($path, $target)
    {
        $path = is_null($path) ? $this->getParams('path', true) : $path;
        $target = is_null($target) ? $this->getParams('target', true) : $target;
        $model = new ImmExtend();
        $bucket = saas_config('storage_engine.oss_bucket');
        $res = $model->convertFile($target, 'oss://' . $bucket . '/' . $path);
        $this->sendResponse(0, $res);
    }
}
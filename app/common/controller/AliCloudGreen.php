<?php
/**
 * Created by PhpStorm.
 * User: LucasXu
 * Date: 2020/3/6
 * Time: 18:41
 * Class AliCloudGreen
 * @package general
 */

namespace app\common\controller;

use aliyun\green\Ocr;
use aliyun\oss\File;
use app\BaseController;

class AliCloudGreen extends BaseController
{
    /**
     * @param $path string
     * @param $taskId string
     * @throws \AlibabaCloud\Client\Exception\ClientException
     * @throws \AlibabaCloud\Client\Exception\ServerException
     * @throws \OSS\Core\OssException
     */
    public function ocrOssGreenImg($path, $taskId)
    {
        $path = is_null($path) ? $this->getParams('path', true) : $path;
        $taskId = is_null($taskId) ? $this->getParams('taskId', true) : $taskId;
        $bucket = saas_config('storage_engine.oss_bucket');
        $path = str_replace('oss://' . $bucket . '/', '', $path);
        $oss = new File();
        $exist = $oss->fileExists($path);
        if ($exist === false)
        {
            saas_send_response(2000, [], '文件不存在');
        }
        $url = $oss->getOriginTempUrl($path);
        $model = new Ocr();
        $res = $model->ossImg($url, $taskId);
        $this->sendResponse(0, $res);
    }
}
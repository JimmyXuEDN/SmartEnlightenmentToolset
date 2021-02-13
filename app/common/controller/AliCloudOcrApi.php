<?php


namespace app\common\controller;


use aliyun\ocr\lib;
use aliyun\oss\File;
use app\BaseController;

class AliCloudOcrApi extends BaseController
{
    /**
     * OCR识别OSS中的图片
     * @param $path
     * @return bool|string
     * @throws \OSS\Core\OssException
     */
    public function ocrOssImg($path)
    {
        $path = is_null($path) ? $this->getParams('path', true) : $path;
        $oss = new File();
        $exist = $oss->fileExists($path);
        if ($exist === false)
        {
            saas_send_response(2000, [], '文件不存在');
        }
        $url = $oss->getOriginTempUrl($path);
        $model = new lib();
        $res = $model->img_ocr($url);
        $this->sendResponse(0, $res);
    }
}
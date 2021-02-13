<?php
// +----------------------------------------------------------------------
// | LinkT [Tech makes service easier]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.linkt.cc All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( 版权所有 )
// +----------------------------------------------------------------------
// | Author: XuKalam <316392750@qq.com>
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
// | upload
// +----------------------------------------------------------------------
namespace qiniu;
// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;

class QiNiuUpload
{
  /*
   * 上传server文件到七牛
   * param
   *  $filePath文件本地路径
   *  $key 七牛保存文件名
   */
  public static function uploadLocalFile ($filePath, $key = '') {
    if (empty($key)) {
      $key = uniqid ('uploadLocalFile_');
    }
    $access_key = saas_config('storage_engine.qiniu_access_key');
    $secret_key = saas_config('storage_engine.qiniu_secret_key');
    $bucket_name = saas_config('storage_engine.qiniu_bucket_name');
    $image_domain = saas_config('storage_engine.qiniu_image_domain');
    $qiniu_put_result = [];
    $qiniu_put_result['result'] = false;
    if (empty($access_key) || empty($secret_key) || empty($bucket_name) || empty($image_domain)) {
      $qiniu_put_result['info'] = lang('UPLOAD_QINIU_LACK_CONFIG');
    } else {
      // 构建鉴权对象
      $auth = new Auth($access_key, $secret_key);
      // 生成上传 Token
      $token = $auth->uploadToken($bucket_name);
      if (!$token) {
        $qiniu_put_result['info'] = lang('UPLOAD_QINIU_TOKEN_ERROR');
      } else {
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 上传到七牛后保存的文件名
        // $key = $file_data['save_file_name'];
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
          $qiniu_put_result['info'] = $err;
        } else {
          $qiniu_put_result['result'] = true;
          $qiniu_put_result['info'] = $ret;
          $qiniu_put_result['url'] = $image_domain . '/' . $ret['key'];
        }
      }
    }
    return $qiniu_put_result;
  }
}
?>

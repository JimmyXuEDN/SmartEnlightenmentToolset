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
namespace app\general\Controller;
use app\common\controller\Base;
// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;
use qiniuUpload\exceptions\QiNiuException;
use qiniuUpload\QiNiuService;

class Upload extends Base
{
    /**
     * 本地上传
     */
    function save () {
        $file = request()->file('files');
        if (empty($file)) {
          $this->sendResponse(1001);
        }
        $upload_path = saas_config('storage_engine.upload_path');
        if (empty($upload_path)) {
          $this->sendResponse(1000, null, lang('UPLOAD_PATH_UNDEFINE'));
        }
        $upload_validate = array();
        $upload_file_size = saas_config('storage_engine.upload_file_size');
        if (!empty($upload_file_size)) {
          $upload_validate['size'] = $upload_file_size * 1024 * 1024;
        }
        $upload_ext = saas_config('storage_engine.upload_ext');
        if (!empty($upload_ext)) {
          $upload_validate['ext'] = $upload_ext;
        }
        $info = $file->validate($upload_validate)->move(ROOT_PATH . 'public' . DS . $upload_path);
        if($info){
          // 保存文件信息
          $file_data['url'] = request()->domain() . '/public' . DS . $info->getSaveName();
          $file_data['file_name'] = ROOT_PATH . 'public' . DS . $upload_path . DS . $info->getSaveName();
          $file_data['save_name'] = $info->getSaveName();
          $file_data['save_file_name'] = $info->getFilename();
          $file_data['ext'] = $info->getExtension();
          $file_data['name'] = $info->getInfo('name');
          $file_data['type'] = $info->getInfo('type');
          $file_data['size'] = $info->getInfo('size');
          $file_record_id = model('GlobalFile')->save($file_data);
          $file_record_id = model('GlobalFile')->id;
          $upload_type = saas_config('storage_engine.is_use');
          if ($upload_type === "1") {
            // chown($file_data['file_name'], 777);
            //如果开启了通过后台上传到七牛，则调用七牛的上传方法
            $ret = $this->qiniu_upload($file_data);
            if (!$ret) {
              $this->unlink_local_file($file_data, $file_record_id);
              $this->sendResponse(1000, null, lang('UPLOAD_QINIU_RETURN_FALSE'));
            } elseif (!$ret['result']) {
              $this->unlink_local_file($file_data, $file_record_id);
              $this->sendResponse(1000, null, lang('UPLOAD_QINIU_RETURN_FALSE'));
            } else {
              $file_data['url'] = $ret['url'];
              $file_data['id'] = $file_record_id;
              model('GlobalFile')->save(['id' => $file_record_id, 'url' => $ret['url'], 'hash' => $ret['info']['hash']]);
              $this->sendResponse(0, $file_data, 'id:' . $file_record_id . 'database res:' . model('GlobalFile')->getLastSql());
            }
          } else {
            // 成功上传后直接返回
            $this->sendResponse(0, $file_data);
          }
        }else{
          // 上传失败获取错误信息
          $this->sendResponse(2000, null, $file->getError());
        }
      }

    function qiniu_upload ($file_data) {
        $access_key = saas_config('storage_engine.qiniu_access_key');
        $secret_key = saas_config('storage_engine.qiniu_secret_key');
        $bucket_name = saas_config('storage_engine.qiniu_bucket_name');
        $image_domain = saas_config('storage_engine.qiniu_image_domain');
        if (empty($access_key) || empty($secret_key) || empty($bucket_name) || empty($image_domain)) {
          $this->sendResponse(1000, array(), lang('UPLOAD_QINIU_LACK_CONFIG'));
        } else {
          // 构建鉴权对象
          $auth = new Auth($access_key, $secret_key);
          // 生成上传 Token
          $token = $auth->uploadToken($bucket_name);
          if (!$token) {
            $this->sendResponse(1000, array(), lang('UPLOAD_QINIU_TOKEN_ERROR'));
          } else {
            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();
            // 要上传文件的本地路径
            $filePath = $file_data['file_name'];
            // 上传到七牛后保存的文件名
            $key = $file_data['save_file_name'];
            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            $qiniu_put_result['result'] = false;
            if ($err !== null) {
              $qiniu_put_result['info'] = $err;
            } else {
              // 上传成功后删除本地文件
              unlink($file_data['file_name']);
              $qiniu_put_result['result'] = true;
              $qiniu_put_result['info'] = $ret;
              $qiniu_put_result['url'] = $image_domain . '/' . $ret['key'];
            }
            return $qiniu_put_result;
          }
        }
    }

    function unlink_local_file ($file_data, $file_record_id) {
        if (unlink($file_data['file_name'])) {
          $ret = model('GlobalFile')->delete($file_record_id);
          if ($ret > 0) {
            return true;
          }
          return false;
        }
    }

    public function token(QiNiuService $service) {
          $result = [];
          try {
              $result['uptoken'] = $service->getUploadToken();
              saas_send_response(SUCCESS, $result);
          } catch (QiNiuException $exception) {
              $result['info'] = $exception->getMessage();
              saas_send_response(ERROR_LOGIC, $result);
          }
          // return $result;
    }

    public function local(){
        $file = request()->file('files');
        if (empty($file)) {
            $this->sendResponse(1001);
        }
        $upload_path = saas_config('storage_engine.upload_path');
        if (empty($upload_path)) {
            $this->sendResponse(1000, null, lang('UPLOAD_PATH_UNDEFINE'));
        }
        $info = $file->move(ROOT_PATH . 'public' . DS . $upload_path);
        if(!$info) {
            // 上传失败获取错误信息
            $this->sendResponse(2000, null, $file->getError());
        }
        $res['local_path'] = $info->getSaveName();

        $this->sendResponse(SUCCESS,$res);

    }

    public function oss_config()
    {
        $id= saas_config('storage_engine.oss_access_key_id');          // 请填写您的AccessKeyId。
        $key= saas_config('storage_engine.oss_access_key_secret');      // 请填写您的AccessKeySecret。
        // $host的格式为 bucketname.endpoint，请替换为您的真实信息。
        $host = 'youshijie.oss-cn-shenzhen.aliyuncs.com';
        // $callbackUrl为上传回调服务器的URL，请将下面的IP和Port配置为您自己的真实URL信息。
        $callbackUrl = 'http://88.88.88.88:8888/aliyun-oss-appserver-php/php/callback.php';
        $dir = 'user-upload/';          // 用户上传文件时指定的前缀。

        $callback_param = array('callbackUrl'=>$callbackUrl,
            'callbackBody'=>'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
            'callbackBodyType'=>"application/x-www-form-urlencoded");
        $callback_string = json_encode($callback_param);

        $base64_callback_body = base64_encode($callback_string);
        $now = time();
        $expire = 30;  //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问。
        $end = $now + $expire;
        $expiration = $this->gmt_iso8601($end);


        //最大文件大小.用户可以自己设置
        $condition = array(0=>'content-length-range', 1=>0, 2=>1048576000);
        $conditions[] = $condition;

        // 表示用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
        $start = array(0=>'starts-with', 1=>'$key', 2=>$dir);
        $conditions[] = $start;


        $arr = array('expiration'=>$expiration,'conditions'=>$conditions);
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

        $response = array();
        $response['accessid'] = $id;
        $response['host'] = $host;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['callback'] = $base64_callback_body;
        $response['dir'] = $dir;  // 这个参数是设置用户上传文件时指定的前缀。
        return $response;
        exit;
    }

    private function gmt_iso8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }
}

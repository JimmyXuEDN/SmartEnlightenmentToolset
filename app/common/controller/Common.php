<?php
namespace app\common\controller;

use app\BaseController;
use app\base\model\GlobalBank;
use app\base\model\GlobalConfig;
use app\base\model\GlobalIndustryCategory;
use app\base\model\GlobalModule;
use app\base\model\GlobalMotto;
use wx\official\lib\Util;

/**
 * 通用接口,一些工具类的接口都 放在这里
 * @author Zhong
 *
 */
class Common extends BaseController
{
    /**
     * 系统信息
     */
    public function system()
    {
        $data['operating_system'] = PHP_OS;//操作系统
        $data['operating_environment'] = $_SERVER["SERVER_SOFTWARE"];//运行环境
        $data['operating_mode'] = php_sapi_name();//运行方式
        $data['operating_mysql'] = "";//MYSQL版本
        $data['operating_max_filesize'] = ini_get('upload_max_filesize');//上传附件限制
        $data['operating_execution_time'] = ini_get('max_execution_time')."秒";//执行时间限制
        $data['operating_free_space'] = round((@disk_free_space(".") / (1024 * 1024)), 2)."M";//剩余空间
        $res['system'] = $data;
        return $this->sendResponse(0, $res);
    }

    /**
     * 模块
     */
    public function module()
    {
        $model = new GlobalModule();
        $res['list'] = $model
            ->where([
                'is_installed' => 1,
                'is_available' => 1
            ])
            ->with(['globalModuleControllers'])
            ->order('list_order DESC,create_time DESC')
            ->select();
        return $this->sendResponse(0, $res);
    }

    /**
     * TODO 移动到对应的扩展
     * 手机号码发送短信验证码
     * @param $tel
     */
    public function sendSms($tel)
    {
        if(!saas_is_tel($tel))
        {
            $this->sendResponse(ERROR_LOGIC, null, 'error_not_tel');
        }
        \sms\SmsUtil::sendAuthMessage($tel);
        $this->sendResponse(0);
    }

    /**
     * TODO 移动到鉴权
     * 短信验证码核验
     * @param $tel
     * @param $code
     */
    public function checkSms($tel, $code)
    {
        $result['check'] = 0;
        if(\sms\SmsUtil::checkAuthMessage($tel, $code))
        {
            $result['check'] = 1;
        }
        $this->sendResponse(0, $result);
    }

    /**
     * TODO 移动到扩展
     * 获取公众号网页js签名
     */
    public function getJsapiSignatrue(){
        $url = $this->getParams('url');
        \wx\official\lib\Config::setConfig(saas_config('wx_official'));
        $res = Util::getJsapiSignatrue($url);
        $this->sendResponse(0, $res);
    }

    /**
     * TODO 移动到扩展
     * 获取openid
     * @param $code
     */
    public function getWxOpenid($code)
    {
        $third_login = new \wx\official\lib\ThirdLogin();
        $result = $third_login->get_user_info("snsapi_base", $code);
        if ($result ['status'] == 2000) {
            $this->sendResponse (ERROR_LOGIC, null, json_encode($result['data']));
        }
        $res['openid'] = $result['data']['openid'];
        $this->sendResponse(0, $res);
    }

    /**
     * 行业分类
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function industry_category()
    {
       $res = GlobalIndustryCategory::select();
       return $this->sendResponse(0, $res);
    }

    /**
     * 银行列表
     * @throws \think\exception\DbException
     */
    public function bank()
    {
       $res = GlobalBank::select();
       return $this->sendResponse(0, $res);
    }

    /**
     * 随机获取一条motto
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOneRandomMotto()
    {
        $model = new GlobalMotto();
        $count = $model->count();
        $rand = mt_rand(0, $count - 1);
        $res = $model->find($rand);
        return $this->sendResponse(0, $res);
    }

}

<?php
namespace app\common\controller;

use app\BaseController;
use app\common\model\GlobalBank;
use app\common\model\GlobalIndustryCategory;
use app\common\model\GlobalMotto;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\response\Json;

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
     * 行业分类
     * @return Json
     * @throws DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws ModelNotFoundException
     */
    public function industry_category()
    {
       $res = GlobalIndustryCategory::select();
       return $this->sendResponse(0, $res);
    }

    /**
     * 银行列表
     * @return Json
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws \think\db\exception\DbException
     */
    public function bank()
    {
       $res = GlobalBank::select();
       return $this->sendResponse(0, $res);
    }

    /**
     * 随机获取一条motto
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
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

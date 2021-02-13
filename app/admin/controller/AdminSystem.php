<?php

namespace app\admin\controller;

use app\base\controller\AdminBaseController;
use app\base\model\SystemConfig;

class AdminSystem extends AdminBaseController
{
    //返回系统信息
    public function index()
    {
        $data['operating_system'] = PHP_OS;//操作系统
        $data['operating_environment'] = $_SERVER["SERVER_SOFTWARE"];//运行环境
        $data['operating_mode'] = php_sapi_name();//运行方式

        /* mysql_connect('47.93.6.2','saas','ABCCBA3@!');
         $version=mysql_get_server_info();*/
        $data['operating_mysql'] = "";//MYSQL版本

        //程序版本号
        $map['config_name'] = 'version';
        $sys = SystemConfig::where($map)->find();
        $data['operating_version'] = $sys['config_val'];

        $data['operating_max_filesize'] = ini_get('upload_max_filesize');//上传附件限制
        $data['operating_execution_time'] = ini_get('max_execution_time') . "秒";//执行时间限制
        $data['operating_free_space'] = round((@disk_free_space(".") / (1024 * 1024)), 2) . "M";//剩余空间
        $res['system'] = $data;
        return $this->sendResponse(SUCCESS, $res);
    }
}
<?php

namespace app\admin\controller;

use app\base\controller\AdminBaseController;
use app\base\model\SystemConfigType;

class AdminSystemConfigType extends AdminBaseController
{

    //返回系统信息
    public function index()
    {
        $res = SystemConfigType::getAdminList();
        return $this->sendResponse(SUCCESS, $res);
    }

    //跳转添加页面
    public function create()
    {

    }

    //添加
    public function save()
    {

    }

    //查看
    public function read($id)
    {

    }

    //跳转修改
    public function edit()
    {

    }

    //修改配置
    public function update($id)
    {
        $config = SystemConfigType::find($id);
        $config->save($this->getParams());
        return $this->sendResponse(SUCCESS);
    }

    //删除
    public function delete($id)
    {

    }
}
<?php

namespace app\admin\controller;

use app\base\controller\AdminBaseController;

class GlobalModuleController extends AdminBaseController
{
    //列表
    public function index()
    {
        $res = \app\base\model\GlobalModuleController::getList([], [], ['type' => 0]);
        return $this->sendResponse(SUCCESS, $res);
    }

    //跳转添加页面
    public function create()
    {

    }

    //添加用户
    public function save()
    {

    }

    //查看
    public function read($id)
    {
        $controller = \app\base\model\GlobalModuleController::find($id);
        $controller->globalModule;
        $res['controller'] = $controller;
        return $this->sendResponse(SUCCESS, $res);
    }

    //跳转
    public function edit()
    {

    }

    //修改
    public function update($id)
    {

    }

    //删除
    public function delete($id)
    {
    }
}
<?php

namespace app\admin\controller;

use app\base\controller\AdminBaseController;

class AdminRole extends AdminBaseController
{
    /**
     * 列表
     */
    public function index()
    {
        $res = \app\admin\model\AdminRole::getList(['accesses']);
        return $this->sendResponse(0, $res);
    }


    /**
     * @param $id
     * @return \think\response\Json
     */
    public function read($id)
    {
        $res = \app\admin\model\AdminRole::with(['accesses'])->find($id);
        return $this->sendResponse(0, $res);
    }

    /**
     * 添加
     */
    public function save()
    {
        \app\admin\model\AdminRole::create($this->getParams());
        return $this->sendResponse(0);
    }

    public function update($id)
    {
        $adminRole = \app\admin\model\AdminRole::find($id);
        $adminRole->save($this->getParams());
        return $this->sendResponse(0);
    }

    /**
     * @param $id
     * @return \think\response\Json
     */
    public function delete($id)
    {
        \app\admin\model\AdminRole::destroy($id);
        return $this->sendResponse(0);
    }
}
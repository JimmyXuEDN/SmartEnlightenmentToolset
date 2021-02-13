<?php

namespace app\admin\controller;

use app\base\controller\AdminBaseController;
use app\base\model\GlobalModuleController;

class GlobalModule extends AdminBaseController
{
    /**
     * 列表
     */
    public function index()
    {
        $res = \app\base\model\GlobalModule::getList([], [], ['is_installed' => 1, 'is_available' => 1]);
        foreach ($res['list'] as $k => $v) {
            $res['list'][$k]['controllers'] = GlobalModuleController::where(['type' => 0, 'module_id' => $v['id']])->select();
        }
        return $this->sendResponse(SUCCESS, $res);
    }

    public function create()
    {
    }

    public function save()
    {
    }

    /**
     * @param $id
     * @return \think\response\Json
     */
    public function read($id)
    {
        $res = \app\base\model\GlobalModule::getDetail($id);
        return $this->sendResponse(SUCCESS, $res);
    }

    /**
     * 删除
     * @param $id
     */
    public function delete($id)
    {
    }
}
<?php

namespace app\admin\controller;

use app\base\controller\AdminBaseController;

class AdminUser extends AdminBaseController
{
    public function index()
    {
        $res = \app\admin\model\AdminUser::getList();
        foreach ($res['list'] as $k => $v) {
            $res['list'][$k]['role'] = \app\admin\model\AdminRole::find($v['role_id']);
        }
        return $this->sendResponse(0, $res);
    }

    /**
     * 添加
     */
    public function save()
    {
        \app\admin\model\AdminUser::create($this->getParams());
        return $this->sendResponse(0);
    }

    /**
     * @param $id
     * @return \think\response\Json
     */
    public function read($id)
    {
        if ($id == 0) {
            $res['user'] = $this->getAdmin();
        } else {
            $res['user'] = \app\admin\model\AdminUser::find($id);
        }
        return $this->sendResponse(0, $res);
    }


    /**
     * @param $id
     * @return \think\response\Json
     */
    public function update($id)
    {
        $model = \app\admin\model\AdminUser::find($id);
        $data = $this->getParams();
        if (isset($data['password']) && $data['password'] == $model->password) {
            unset($data['password']);
        }
        $model->save($data);
        return $this->sendResponse(0);
    }

    /**
     * @param $id
     * @return \think\response\Json
     */
    public function delete($id)
    {
        \app\admin\model\AdminUser::destroy($id);
        return $this->sendResponse(0);
    }
}
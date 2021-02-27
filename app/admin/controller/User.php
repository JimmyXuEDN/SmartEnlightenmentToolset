<?php

namespace app\admin\controller;

use app\admin\model\AdminUser;
use app\admin\validate\AdminUserValidate;
use app\base\controller\AdminBaseController;
use app\base\exception\SaasException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\response\Json;

class User extends AdminBaseController
{
    public function index()
    {
        $res = AdminUser::getList(['role']);
        return $this->sendResponse(0, $res);
    }

    /**
     * 添加
     */
    public function save()
    {
        $this->validate($this->getParams(), AdminUserValidate::class);
        AdminUser::create($this->getParams());
        return $this->sendResponse(0);
    }

    /**
     * @param $id
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function read($id)
    {
        if ($id == 0) {
            $res['user'] = $this->getAdmin();
        } else {
            $res['user'] = AdminUser::find($id, ['role']);
        }
        return $this->sendResponse(0, $res);
    }


    /**
     * @param $id
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws SaasException
     */
    public function update($id)
    {
        $model = AdminUser::find($id);
        $data = $this->getParams();
        if (isset($data['password']) && $data['password'] == $model->password) {
            unset($data['password']);
        }
        $model->save($data);
        return $this->sendResponse(0);
    }

    /**
     * @param $id
     * @return Json
     */
    public function delete($id)
    {
        AdminUser::destroy($id);
        return $this->sendResponse(0);
    }
}
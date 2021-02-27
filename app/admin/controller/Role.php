<?php

namespace app\admin\controller;

use app\admin\model\AdminRole;
use app\admin\validate\AdminRoleValidate;
use app\base\controller\AdminBaseController;
use app\base\exception\SaasException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\response\Json;

class Role extends AdminBaseController
{
    /**
     * @return Json
     */
    public function index()
    {
        $res = AdminRole::getList(['accesses']);
        return $this->sendResponse(0, $res);
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
        $res = AdminRole::with(['accesses'])->find($id);
        return $this->sendResponse(0, $res);
    }

    /**
     * @return Json
     * @throws SaasException
     */
    public function save()
    {
        $this->validate($this->getParams(), AdminRoleValidate::class);
        AdminRole::create($this->getParams());
        return $this->sendResponse(0);
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
        $adminRole = AdminRole::find($id);
        $adminRole->save($this->getParams());
        return $this->sendResponse(0);
    }

    /**
     * @param $id
     * @return Json
     */
    public function delete($id)
    {
        AdminRole::destroy($id);
        return $this->sendResponse(0);
    }
}
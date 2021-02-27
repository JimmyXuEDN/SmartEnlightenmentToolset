<?php

namespace app\member\controller;

use app\base\controller\AdminBaseController;
use app\base\exception\SaasException;
use app\member\model\MemberReal;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Response;

class AdminMemberReal extends AdminBaseController
{

    /**
     * 显示资源列表
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function index()
    {
        $res = MemberReal::getList(['member']);
        return $this->sendResponse(0, $res);
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function read($id)
    {
        $res = MemberReal::with(['member'])->find($id);
        return $this->sendResponse(0, $res);
    }

    /**
     * 保存更新的资源
     * @param int $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws SaasException
     */
    public function update($id)
    {
        $model = MemberReal::find($id);
        $model->save($this->getParams());
        return $this->sendResponse(0);
    }

    /**
     * 删除指定资源
     * @param  int $id
     * @return Response
     */
    public function delete($id)
    {
        MemberReal::destroy($id);
        return $this->sendResponse(SUCCESS);
    }
}
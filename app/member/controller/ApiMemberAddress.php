<?php

namespace app\member\controller;

use app\base\controller\ApiBaseController;
use app\base\exception\SaasException;
use app\member\model\MemberAddress;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Request;
use think\Response;

class ApiMemberAddress extends ApiBaseController
{
    /**
     * 显示资源列表
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws SaasException
     */
    public function index()
    {
        $res = MemberAddress::getAddressList($this->getMemberId());
        return $this->sendResponse(SUCCESS, $res);
    }

    /**
     * 保存新建的资源
     * @return Response
     * @throws SaasException
     */
    public function save()
    {
        $this->getMember()->memberAddress()->save($this->getParams());
        return $this->sendResponse(0);
    }

    /**
     * 显示指定的资源
     * @param int $id
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws SaasException
     */
    public function read($id)
    {
        $map['id'] = $id;
        $map['member_id'] = $this->getMemberId();
        $res = MemberAddress::find($map);
        return $this->sendResponse(SUCCESS, $res);
    }

    /**
     * 保存更新的资源
     * @param int $id
     * @return Response
     * @throws SaasException
     */
    public function update($id)
    {
        $map['id'] = $id;
        $map['member_id'] = $this->getMemberId();
        $this->getMember()->memberAddress()->where($map)->save($this->getParams());
        return $this->sendResponse(0);
    }

    /**
     * 删除指定资源
     * @param int $id
     * @return Response
     * @throws SaasException
     */
    public function delete($id)
    {
        $map['id'] = $id;
        $map['member_id'] = $this->getMemberId();
        MemberAddress::where($map)->save(['status' => 0]);
        return $this->sendResponse(SUCCESS);
    }
}
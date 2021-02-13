<?php

namespace app\member\controller;

use app\base\controller\ApiBaseController;
use app\member\model\MemberAddress;

class ApiMemberAddress extends ApiBaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $member_id = $this->getMember()->member_id;
        $res = MemberAddress::getAddressList($member_id);
        return $this->sendResponse(SUCCESS, $res);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //

    }

    /**
     * 保存新建的资源
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save()
    {
        //添加地址
        if ($this->getParams('is_default', false) == 1) {
            $update['is_default'] = 0;
            $this->getMember()->memberAddress->update($update);
        }
        $this->getMember()->memberAddress()->save($this->getParams());

        return $this->sendResponse(0);
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        $res = MemberAddress::find($id);
        return $this->sendResponse(SUCCESS, $res);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {

    }

    /**
     * 保存更新的资源
     *
     * @param \think\Request $request
     * @param int $id
     * @return \think\Response
     */
    public function update($id)
    {
        //添加地址
        if ($this->getParams('is_default', false) == 1) {
            $update['is_default'] = 0;
            $this->getMember()->memberAddress->update($update);
        }

        $this->getMember()->memberAddress()->where('addr_id', $id)->save($this->getParams());

        return $this->sendResponse(0);
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $map['addr_id'] = $id;
        $res = MemberAddress::where($map)->save(['status' => 0]);
        return $this->sendResponse(SUCCESS);
    }
}
<?php

namespace app\member\controller;

use app\base\controller\ApiBaseController;
use app\member\model\MemberBank;

class ApiMemberBank extends ApiBaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $res = MemberBank::getBankList($this->getMemberId());
        return $this->sendResponse(SUCCESS,$res);
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
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save()
    {
        //绑定银行卡
        $this->getMember()->bank()->save($this->getParams());
        return $this->sendResponse(0);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $res = MemberBank::getBankDetail($id);
        return $this->sendResponse(SUCCESS,$res);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {

    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update($id)
    {
        //修改地址
        $this->getMember()->bank()->where('id', $id)->save($this->getParams());
        return $this->sendResponse(SUCCESS);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        MemberBank::destroy($id);
        return $this->sendResponse(ERROR_SQL);
    }
}
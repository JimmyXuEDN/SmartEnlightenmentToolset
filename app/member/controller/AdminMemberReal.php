<?php

namespace app\member\controller;

use app\base\controller\AdminBaseController;
use app\member\model\MemberReal;

class AdminMemberReal extends AdminBaseController
{

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $res = MemberReal::getList(['member']);
        return $this->sendResponse(0, $res);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request
     * @return \think\Response
     */
    public function save()
    {
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        $res = MemberReal::with(['member'])->find($id);
        return $this->sendResponse(0, $res);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request
     * @param  int $id
     * @return \think\Response
     */
    public function update($id)
    {
        $data['is_verify'] = $this->getParams('is_verify', true);
        $data['verify_message'] = $this->getParams('verify_message', false);
        $model = MemberReal::find($id);
        $model->save($data);
        return $this->sendResponse(0);
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
    }
}
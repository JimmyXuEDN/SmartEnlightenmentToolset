<?php

namespace app\member\controller;

use app\base\controller\AdminBaseController;
use app\member\model\MemberFeedback;

class AdminMemberFeedback extends AdminBaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $res = MemberFeedback::getList(['member']);
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


    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {

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
        $feed = MemberFeedback::find($id);
        $feed->status = $this->getParams('status');
        $feed->save();
        return $this->sendResponse(SUCCESS);
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        MemberFeedback::destroy($id);
        return $this->sendResponse(SUCCESS);
    }

}
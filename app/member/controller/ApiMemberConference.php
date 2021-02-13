<?php

namespace app\member\controller;

use app\base\controller\ApiBaseController;
use app\member\model\MemberConference;

class ApiMemberConference extends ApiBaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
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
        $data['member_id'] = $this->getMemberId();
        $data['conference_id'] = $this->getParams('conference_id', true);
        $model = MemberConference::where($data)->find();
        if (is_null($model))
        {
            $model = new MemberConference();
            $model->save($data);
        }
        return $this->sendResponse(0);
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
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
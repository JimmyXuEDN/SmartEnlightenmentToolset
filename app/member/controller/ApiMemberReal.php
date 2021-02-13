<?php

namespace app\member\controller;

use app\base\controller\ApiBaseController;
use app\member\model\MemberReal;

class ApiMemberReal extends ApiBaseController
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
        $member = $this->getMember();
        $data = $this->getParams();
        if ($member->memberReal) {
            $data['is_verify'] = 0;
            $data['verify_message'] = '';
            $member->memberReal->save($data);
        } else {
            $member->memberReal()->save($data);
        }
        return $this->sendResponse(SUCCESS);
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        $res = MemberReal::where(['member_id' => $this->getMemberId()])->find();
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
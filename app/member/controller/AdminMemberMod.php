<?php

namespace app\member\controller;

use app\base\controller\AdminBaseController;
use app\member\model\Member;
use app\member\model\MemberMod;

class AdminMemberMod extends AdminBaseController
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
        $model = MemberMod::find($id);
        $member = Member::find($model->member_id);
        $member->unit = $model->unit;
        $member->capacity = $model->capacity;
        $member->qr_code = $model->qr_code;
        $member->nick_name = $model->nick_name;
        $member->avatar = $model->avatar;
        $member->gender = $model->gender;
        $member->birthday = $model->birthday;
        $member->email = $model->email;
        $member->address = $model->address;
        $member->jie = $model->jie;
        $member->party = $model->party;
        $member->street = $model->street;
        $member->station = $model->station;
        $member->occupy_time = $model->occupy_time;
        $member->save();
        MemberMod::destroy($id);
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
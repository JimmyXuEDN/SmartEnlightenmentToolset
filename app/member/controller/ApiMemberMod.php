<?php

namespace app\member\controller;

use app\base\controller\ApiBaseController;
use app\member\model\MemberMod;
use app\message\model\Message;

class ApiMemberMod extends ApiBaseController
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
        $data = $this->getParams();
        $model = new MemberMod();
        $data['member_id'] = $this->getMemberId();
        $model->save($data);
        // 生成消息
        $message_array['name'] = $this->getMember()->nick_name;
        $message_array['date'] = date('Y-m-d H:i:s', time());
        Message::sendUserMessageByTemplate(0, "edit_member_info", $message_array);
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
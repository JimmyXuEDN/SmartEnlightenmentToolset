<?php

namespace app\auth\controller;

use app\base\controller\AdminBaseController;
use app\member\model\AuthMember;

class AdminAuthMember extends AdminBaseController
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
     * @param \think\Request
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
     * @param \think\Request
     * @param int $id
     * @return \think\Response
     */
    public function update($id)
    {
        $model = AuthMember::find($id);
        $data['identifier'] = $this->getParams('identifier', true);
        $credential = $this->getParams('credential', false);
        if (isset($credential) && !empty($credential)) {
            $data['credential'] = saas_password($credential);
        }
        $model->save($data);
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
    }
}
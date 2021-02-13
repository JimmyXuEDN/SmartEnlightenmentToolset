<?php

namespace app\admin\controller;

use app\base\controller\AdminBaseController;

class AdminAccess extends AdminBaseController
{
    /**
     * 显示资源列表
     *
     */
    public function index()
    {
        $res = \app\admin\model\AdminAccess::getList();
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
     * @param \think\Request
     * @return \think\Response
     */
    public function save()
    {
        $ids = $this->getParams('ids/a', true);
        $children = $this->getParams('children/a', true);
        $role_id = $this->getParams('role_id', true);

        $access_data = [];
        foreach ($ids as $key => $id) {
            $array = [
                'role_id' => $role_id,
                'controller_id' => $id,
                'operate_id ' => $children[$key]
            ];
            $access_data[] = $array;
        }
        \app\admin\model\AdminAccess::where('role_id', $role_id)->delete();
        \app\admin\model\AdminAccess::saveAll($access_data);
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
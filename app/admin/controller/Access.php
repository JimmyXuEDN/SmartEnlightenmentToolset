<?php

namespace app\admin\controller;

use app\admin\model\AdminAccess;
use app\base\controller\AdminBaseController;
use app\base\exception\SaasException;
use think\Response;

class Access extends AdminBaseController
{
    /**
     * 显示资源列表
     *
     */
    public function index()
    {
        $res = AdminAccess::getList();
        return $this->sendResponse(0, $res);
    }

    /**
     * 保存新建的资源
     * @return Response
     * @throws SaasException
     */
    public function save()
    {
        $ids = $this->getParams('ids/a', true);
        $role_id = $this->getParams('admin_role_id', true);

        $access_data = [];
        foreach ($ids as $key => $id) {
            $array = [
                'admin_role_id' => $role_id,
                'global_application_route_id' => $id
            ];
            $access_data[] = $array;
        }
        $model = new AdminAccess();
        $model->where('admin_role_id', $role_id)->delete();
        $model->saveAll($access_data);
        return $this->sendResponse(0);
    }

    /**
     * 显示指定的资源
     * @param int $id
     * @return void
     */
    public function read($id)
    {
    }

    /**
     * 保存更新的资源
     *
     * @param int $id
     * @return void
     */
    public function update($id)
    {
    }

    /**
     * 删除指定资源
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
    }
}
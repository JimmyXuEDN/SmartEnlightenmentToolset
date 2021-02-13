<?php

namespace app\mp\controller;

use app\base\controller\AdminBaseController;
use app\mp\model\Mp;

class AdminMp extends AdminBaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $res = Mp::getList();
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
        Mp::create($this->getParams());
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
        $res = Mp::find($id);
        return $this->sendResponse(0, $res);
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
        $mp = Mp::find($id);
        $mp->save($this->getParams());
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
        Mp::destroy($id);
        return $this->sendResponse(0);
    }
}
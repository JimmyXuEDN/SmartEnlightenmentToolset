<?php
//前台
namespace app\advertisement\controller;

use app\advertisement\model\Advertisement;
use app\base\controller\ApiBaseController;

class ApiAdvertisement extends ApiBaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $res = Advertisement::getList();
        return $this->sendResponse(0,$res);
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
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save()
    {

    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $res = Advertisement::find($id);
        return $this->sendResponse(SUCCESS,$res);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {

    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update($id)
    {

    }

    /**
     * 删除指定积分商品
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
    }
}

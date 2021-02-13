<?php
//后台广告
namespace app\advertisement\controller;

use app\advertisement\model\Advertisement;
use app\base\controller\AdminBaseController;

class AdminAdvertisement extends AdminBaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $res = Advertisement::getAdminList();
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
        Advertisement::create($this->getParams());
        return $this->sendResponse(0);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $res = Advertisement::getDetail($id);
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
        $ad = Advertisement::find($id);
        $ad->save($this->getParams());
        return $this->sendResponse(0);
    }

    /**
     * 删除指定积分商品
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        Advertisement::destroy($id);
        return $this->sendResponse(0);
    }
}

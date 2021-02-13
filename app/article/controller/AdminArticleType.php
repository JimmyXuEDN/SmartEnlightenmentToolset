<?php
//后台文章分类管理
namespace app\article\controller;

use app\article\model\ArticleType;
use app\base\controller\AdminBaseController;
use think\console\command\make\Subscribe;

class AdminArticleType extends AdminBaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $res = ArticleType::getArticleTypeList();
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
        //添加文章
        ArticleType::create($this->getParams());
        return $this->sendResponse(SUCCESS);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $res = ArticleType::getDetail($id);
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
        $type = ArticleType::find($id);
        $type->save($this->getParams());
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
        ArticleType::destroy($id);
        return $this->sendResponse(SUCCESS);
    }
}

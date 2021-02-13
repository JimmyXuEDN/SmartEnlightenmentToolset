<?php
//后台积分商品管理
namespace app\article\controller;

use app\article\model\Article;
use app\base\controller\AdminBaseController;

class AdminArticle extends AdminBaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $res = Article::getList(['type']);
        return $this->sendResponse(0, $res);
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
        Article::create($this->getParams());
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
        $res = Article::with(['type'])->find($id);
        return $this->sendResponse(SUCCESS, $res);
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
        $article = Article::find($id);
        $article->save($this->getParams());
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
        Article::destroy($id);
        return $this->sendResponse(0);
    }
}

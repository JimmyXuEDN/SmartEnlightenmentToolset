<?php

namespace app\admin\controller;

use app\base\controller\AdminBaseController;

class Menu extends AdminBaseController
{
    //菜单列表
    public function index()
    {
        $menu = \app\base\model\GlobalModule::getMenu();
        return $this->sendResponse(0, $menu);
    }
}
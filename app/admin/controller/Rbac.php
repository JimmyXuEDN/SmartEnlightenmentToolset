<?php

namespace app\admin\controller;

use app\base\controller\AdminBaseController;

class Rbac extends AdminBaseController
{

    public function index()
    {
        $returnData = [];
        // 用户信息
        $returnData['user'] = $this->getAdmin();
        //该用户菜单权限
        $menu = [];
        $returnData['user']['role_id'];

        $menu = \app\base\model\GlobalModule::getMenu();

        $returnData['menu'] = $menu;

        return $this->sendResponse(0, $returnData);
    }
}
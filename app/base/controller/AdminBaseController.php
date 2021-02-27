<?php

namespace app\base\controller;

use app\admin\model\AdminUser;
use app\base\exception\SaasException;
use app\BaseController;

class AdminBaseController extends BaseController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        $this->checkLogin();
    }

    /**
     * 设置管理信息
     */
    public function checkLogin()
    {
        $this->request->setAdmin(AdminUser::checkLogin($this->request->header('token', '')));
    }

    /**
     * 获取管理用户信息
     * @return AdminUser|null
     */
    public function getAdmin()
    {
        return $this->request->getAdmin();
    }

    /**
     * 获取管理用户主键
     * @return mixed
     * @throws SaasException
     */
    public function getAdminId()
    {
        if (is_null($this->getAdmin())) {
            saas_abort(ERROR_TOKEN);
        }
        return $this->getAdmin()->id;
    }
}
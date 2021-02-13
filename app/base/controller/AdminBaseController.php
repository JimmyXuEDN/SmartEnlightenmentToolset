<?php

namespace app\base\controller;

use app\admin\model\AdminUser;
use app\BaseController;

class AdminBaseController extends BaseController
{

    public function initialize()
    {
        $this->checkLogin();
    }


    public function checkLogin()
    {
        $this->request->setAdmin(AdminUser::checkLogin($this->request->header('token', '')));
        if (!$this->request->getAdmin() && $this->request->isModuleControllerNeedLogin()) {
            saas_abort(ERROR_TOKEN);
        }
    }

    public function getAdmin()
    {
        return $this->request->getAdmin();
    }

    public function getAdminId()
    {
        if (is_null($this->getAdmin())) {
            saas_abort(ERROR_TOKEN);
        }
        return $this->getAdmin()->id;
    }
}
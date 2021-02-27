<?php

namespace app\auth\controller;

use app\admin\model\AdminUser;
use app\base\controller\AdminBaseController;
use app\base\exception\SaasException;
use think\response\Json;

class AdminLogin extends AdminBaseController
{

    /**
     * @return Json
     * @throws SaasException
     */
    public function login()
    {
        $this->validate($this->getParams(), 'app\admin\validate\AdminUserValidate.LoginAccount');

        $user = AdminUser::login($this->getParams('account'), saas_admin_password(saas_rsa_decode($this->getParams('password'))));

        if (is_null($user)) {
            return $this->sendResponse(ERROR_LOGIC, [], '账号或者密码错误');
        }

        if ($user->status != 1) {
            return $this->sendResponse(ERROR_LOGIC, [], '用户已禁用');
        }

        $res['token'] = $user->updateToken();
        $res['user'] = $user;

        return $this->sendResponse(SUCCESS, $res);
    }
}
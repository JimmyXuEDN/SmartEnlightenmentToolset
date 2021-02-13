<?php

namespace app\admin\controller;

use app\base\controller\AdminBaseController;

class AdminPassword extends AdminBaseController
{
    //修改管理员
    public function update($id)
    {

        $old_password = $this->getParams('old_password');
        $password = $this->getParams('password');

        $user = \app\admin\model\AdminUser::find($id);
        if (is_null($user)) {
            return $this->sendResponse(ERROR_LOGIC, [], "用户不存在");
        }
        $o_p = saas_admin_password($old_password);

        if ($user->password != $o_p) {
            return $this->sendResponse(ERROR_LOGIC, null, 'validate_adminlogin_user_old_password_error');
        }
        $user->password = saas_admin_password($password);

        $user->save();
        return $this->sendResponse(SUCCESS);
    }
}
<?php

namespace app\admin\validate;

use think\Validate;

class AdminUser extends Validate
{

    protected $rule = [
        'account|账号'  => 'require',
        'password|密码'   => 'require',
    ];


    /**
     * 账号登录验证
     * @return AdminUser
     */
    public function sceneLoginAccount()
    {
       return $this->only(['account', 'password']);
    }
}
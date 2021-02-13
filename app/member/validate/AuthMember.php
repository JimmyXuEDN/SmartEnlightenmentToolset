<?php

namespace app\member\validate;

use think\Validate;

class AuthMember extends Validate
{

    protected $rule = [
        'account|账号'  => 'require',
        'mobile|手机号码'  => 'require|mobile',
        'password|密码'   => 'require',
        'code'   => 'require',
        'scope'   => 'require',
    ];


    /**
     * 账号登录验证
     * @return AdminUser
     */
    public function sceneLoginAccount()
    {
       return $this->only(['account', 'password']);
    }

    /**
     * 账号登录验证
     * @return AdminUser
     */
    public function sceneLoginMobile()
    {
        return $this->only(['mobile', 'password']);
    }

    /**
     * 账号登录验证
     * @return AdminUser
     */
    public function sceneLoginWxOfficial()
    {
        return $this->only(['code', 'scope']);
    }
}
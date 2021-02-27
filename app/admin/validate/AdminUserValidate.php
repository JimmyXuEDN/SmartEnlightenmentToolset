<?php

namespace app\admin\validate;

use app\base\validate\BaseValidate;

class AdminUserValidate extends BaseValidate
{

    protected $rule = [
        'account|账号'  => 'require',
        'password|密码'   => 'require|isRSAEncodeData',
    ];

    protected $message = [
        'password.isRSAEncodeData' => '密码需要经过加密再提交'
    ];

    /**
     * 账号登录验证
     * @return AdminUserValidate
     */
    public function sceneLoginAccount()
    {
       return $this->only(['account', 'password']);
    }
}
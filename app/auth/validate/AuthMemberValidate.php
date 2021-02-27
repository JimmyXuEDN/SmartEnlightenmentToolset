<?php

namespace app\auth\validate;

use app\base\exception\SaasException;
use app\base\validate\BaseValidate;

class AuthMemberValidate extends BaseValidate
{

    protected $rule = [
        'account|账号'  => 'require',
        'mobile|手机号码'  => 'require|mobile',
        'password|密码'   => 'require|isRSAEncodeData',
        'code'   => 'require',
        'scope'   => 'require',
        'identity_type|鉴权类型'  => 'require',
        'identifier|账号'  => 'require|unique:auth_member',
        'credential|密码'  => 'require|passwordComplexityValidate',
    ];

    protected $message = [
        'identifier.unique' => '该账号/手机号已被占用',
        'password.isRSAEncodeData' => '密码需要经过加密再提交',
        'credential.passwordComplexityValidate' => '要求密码必须包含字母数字和特殊字符（!#$%^&）密码长度8-20'
    ];

    /**
     * @param $data
     * @return false|int
     * @throws SaasException
     */
    public function passwordComplexityValidate($data)
    {
        // 获取配置中的密码等级
        $password_level = intval(saas_config('auth.password_level'));
        // 根据密码等级验证，默认1：大小写字母数字特殊字符四选三且大于8位
        // 要求密码必须包含字母数字和特殊字符（!#$%^&）密码长度8-20
        switch ($password_level) {
            default:
                $res = preg_match("/^(?![\d]+$)(?![a-zA-Z]+$)(?![!#$%^&*]+$)[\da-zA-Z!@#$%^&*]{8,20}$/i", $data);
        }
        $res = $res > 0 ? true : '要求密码必须包含字母数字和特殊字符（!#$%^&）密码长度8-20';
        return $res;
    }

    /**
     * 添加用户场景
     * @return AuthMemberValidate
     */
    public function sceneAddAuth()
    {
        return $this->only(['identity_type', 'identifier', 'credential']);
    }

    /**
     * 账号登录验证
     * @return AuthMemberValidate
     */
    public function sceneLoginAccount()
    {
       return $this->only(['account', 'password']);
    }

    /**
     * 账号登录验证
     * @return AuthMemberValidate
     */
    public function sceneLoginMobile()
    {
        return $this->only(['mobile', 'password']);
    }

    /**
     * 账号登录验证
     * @return AuthMemberValidate
     */
    public function sceneLoginWxOfficial()
    {
        return $this->only(['code', 'scope']);
    }
}
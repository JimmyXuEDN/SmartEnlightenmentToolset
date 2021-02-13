<?php

namespace app\member\model;

use app\base\model\BaseModel;

class AuthMember extends BaseModel
{
    public const SAAS_AUTH_MOBILE = 1; //手机号认证
    public const SAAS_AUTH_WX_OFFICIAL = 2; //微信公众号
    public const SAAS_AUTH_WX_MP = 3; //微信小程序
    public const SAAS_AUTH_WX_PUBLIC = 4; //微信开放平台
    public const SAAS_AUTH_WEIBO = 5; //新浪微博
    public const SAAS_AUTH_ACCOUNT = 6; //账号认证

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    public function setAuthInfoAttr($value)
    {
        return json_encode($value);
    }

    public function getAuthInfoAttr($value)
    {
        return json_decode($value);
    }

    /**
     * 登录认证
     * @param int $type 认证类型 传0则不判断
     * @param string $identifier
     * @param string $credential
     * @param string $openid 传了openid则优先检查openid
     * @return array|\think\Model|null
     */
    public static function auth(int $type, string $identifier, string $credential, string $openid = '')
    {
        if ($openid) {
            $authMember = self::where('open_id', $openid)->find();
            if ($authMember) {
                return $authMember;
            }
        }

        if ($type === 0) {
            $map = [
                'identifier' => $identifier,
            ];
        } else {
            $map = [
                'identity_type' => $type,
                'identifier' => $identifier,
            ];
        }

        if ($credential) {
            $map['credential'] = $credential;
        }

        return self::where($map)->find();
    }

}
<?php

namespace app\member\model;

use app\auth\model\AuthMember;
use app\base\exception\SaasException;
use app\base\model\BaseModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;
use think\model\relation\HasMany;
use think\model\relation\HasOne;

class Member extends BaseModel
{
    /**
     * 有一个token登录凭证
     * @return HasOne
     */
    public function memberToken()
    {
        return $this->hasOne(MemberToken::class);
    }

    /**
     * 有多个鉴权类型
     * 授权类型：1手机号 2微信公众号 3小程序 4QQ 5新浪微博 6账号模式
     * @return HasOne
     */
    public function authMember()
    {
        return $this->hasOne(AuthMember::class);
    }

    /**
     * 有多个地址
     * @return HasMany
     */
    public function memberAddress()
    {
        return $this->hasMany(MemberAddress::class);
    }

    /**
     * 有一个实名认证信息
     * @return HasOne
     */
    public function memberReal()
    {
        return $this->hasOne(MemberReal::class);
    }

    /**
     * sn生成事件
     * @param Member $model
     * @return mixed|void
     */
    public static function onBeforeInsert(Member $model)
    {
        $model->member_sn = $model->genOpenId();
    }

    /**
     * @param $token
     * @return array|Model|Member
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function checkLogin($token)
    {
        return self::hasWhere('memberToken', function ($query) use ($token) {
            $query->where('token', $token);
        })->find();
    }

    /**
     * 注册方法
     * @param array $member_data
     * @param array $auth_data
     * @param string $invitation_code
     * @return Member|Model
     */
    public static function register(array $member_data, array $auth_data, string $invitation_code = '')
    {
        $member = self::create($member_data);
        $member->authMember()->save($auth_data);
        return $member;
    }

    /**
     * token凭证更新
     * @return string
     * @throws SaasException
     */
    public function updateToken()
    {
        $token_type = saas_config('auth.token_type');
        if ($token_type == 2) {
            //多人登录
            if ($this->memberToken) {
                return $this->memberToken->token;
            }
        }

        $data['token'] = uniqid();
        $data['expired_time'] = time() + saas_config('auth.token_expired');

        if ($this->memberToken) {
            $this->memberToken->save($data);
        } else {
            $this->memberToken()->save($data);
        }
        return $data['token'];
    }

    /**
     * 更新用户登录状态
     */
    public function updateLoginStatus()
    {
        $this->last_login_time = time();
        $this->save();
    }

    /**
     * 生成系统openid
     * @return string
     * @throws SaasException
     */
    private function genOpenId()
    {
        // 默认使用UUID
        if (saas_config('member.is_sn_on') != 1) {
            return uniqid();
        }

        // 使用配置规则
        $type = intval(saas_config('member.sn_type'));
        switch ($type) {
            case 1:
                $start = intval(saas_config('member.sn_number_start'));
                $start = empty($start) ? 0 : $start;
                $last_id = self::order('id DESC')->value('id');
                $sn = $start + $last_id + 1;
                break;
            default:
                $sn = uniqid();
                break;
        }
        return saas_config('member.sn_pre') . $sn . saas_config('member.sn_suffix');
    }
}
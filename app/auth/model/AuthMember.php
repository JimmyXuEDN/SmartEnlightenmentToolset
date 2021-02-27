<?php

namespace app\auth\model;

use app\base\model\BaseModel;
use app\member\model\Member;
use app\member\model\MemberToken;
use app\message\model\Message;
use PHPMailer\PHPMailer\Exception;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;
use think\model\relation\BelongsTo;

class AuthMember extends BaseModel
{
    public const SAAS_AUTH_MOBILE = 1; //手机号认证
    public const SAAS_AUTH_WX_OFFICIAL = 2; //微信公众号
    public const SAAS_AUTH_WX_MP = 3; //微信小程序
    public const SAAS_AUTH_WX_PUBLIC = 4; //微信开放平台
    public const SAAS_AUTH_WEIBO = 5; //新浪微博
    public const SAAS_AUTH_ACCOUNT = 6; //账号认证

    /**
     * @return BelongsTo
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'id', 'member_id');
    }

    /**
     * @param $value
     * @return false|string
     */
    public function setAuthInfoAttr($value)
    {
        return json_encode($value);
    }

    /**
     * @param $value
     * @return mixed
     */
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
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
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

    /**
     * 生成一个用户
     * @param $data
     * @throws \think\exception\DbException
     * @throws Exception
     */
    public function genMember($data)
    {
        // 添加用户
        $res = Member::create($data);
        $name = $this->isEmpty($res->getAttr('nick_name')) ? $res->getAttr('identifier') : $res->getAttr('nick_name');
        // 添加auth
        $data['member_id'] = $res->getAttr('id');
        self::create($data);

        // 产生消息
        // 给管理员产生提醒
        Message::sendAllUserMessageByTemplate('register_notice', [
            'name' => $name
        ], 1);
    }

    /**
     * @param $type
     * @param $info
     * @return AuthMember|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function checkLoginWx($type, $info){
        $res = null;
        $map['identity_type'] = $type;
        if (isset($info['unionid'])) {
            $map['identifier'] = $info['unionid'];
            $res = self::find($map);
            if (!is_null($res)) {
                return $res;
            }
        }
        if (isset($info['openid'])) {
            $map['identifier'] = $info['openid'];
            $res = self::find($map);
            if (!is_null($res)) {
                if (isset($info['unionid'])) {
                    $res->identifier = $info['unionid'];
                    $res->save();
                }
                return $res;
            }
        }
        return $res;
    }

    /**
     * @param $info
     * @return MemberThirdlogin|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function checkLoginOrigin($info){
        // 当前用户请求时是登录状态，此时只增加auth_type
        $token = request()->header('token');
        if (isset($token)) {
            $member = MemberToken::checkLogin($token);
            if (!is_null($member) && isset($member->member_id)) { // 用户已登录，写入auth
                $map['identity_type'] = $info['identity_type'];
                $map['identifier'] = $info['identifier'];
                $exist = self::find($map);
                if (is_null($exist)) { // 这个验证类型不存在时，新增
                    $info['member_id'] = $member->member_id;
                    self::selfGenAuth($info);
                } else {
                    return $exist;
                }
            }
        }

        $map['identifier'] = $info['identifier'];
        $res = self::find($map);

        if (!is_null($res)) { // 用户已存在
            $map['identity_type'] = $info['identity_type'];
            $exist = self::find($map);
            if (is_null($exist)) { // 这个验证类型不存在时，新增
                $info['member_id'] = $res->member_id;
                self::selfGenAuth($info);
            }
        }

        return $res;
    }

    /**
     * @param $mobile
     * @return AuthMember|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function checkLoginMobile($mobile)
    {
        $map['identity_type'] = 1;
        $map['identifier'] = $mobile;

        return self::find($map);
    }

    /**
     * TODO 只使用create时，添加数据没有过滤字段问题
     * 单独生成auth记录
     * @param $data
     */
    public static function selfGenAuth($data)
    {
        $add_data = [
            'identifier' => $data['identifier'],
            'auth_info' => isset($data['auth_info']) ? $data['auth_info'] : '',
            'open_id' => $data['open_id'],
            'identity_type' => $data['identity_type'],
            'member_id' => $data['member_id'],
        ];
        if (isset($data['credential'])) {
            $add_data['credential'] = $data['credential'];
        }
        self::create($add_data);
    }
}
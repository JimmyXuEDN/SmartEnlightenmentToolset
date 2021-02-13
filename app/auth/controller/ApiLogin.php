<?php

namespace app\auth\controller;

use app\base\controller\ApiBaseController;
use app\base\util\SmsUtil;
use app\member\model\AuthMember;
use app\member\model\Member;
use app\message\model\Message;
use app\mp\model\Mp;
use wx\official\lib\MpLogin;
use wx\official\lib\ThirdLogin;

class ApiLogin extends ApiBaseController
{

    public function mobile()
    {
        $this->validate($this->getParams(), 'app\member\validate\AuthMember.LoginMobile');

        return $this->login(AuthMember::SAAS_AUTH_MOBILE, $this->getParams('mobile'), saas_password($this->getParams('password')));
    }

    public function account()
    {
        $this->validate($this->getParams(), 'app\member\validate\AuthMember.LoginAccount');

        return $this->login(AuthMember::SAAS_AUTH_ACCOUNT, $this->getParams('account'), saas_password($this->getParams('password')));
    }

    public function isLogin()
    {
        $member = $this->request->getMember();

        $data['is_login'] = $member ? 1 : 0;
        $data['member'] = $member;

        return $this->sendResponse(SUCCESS, $data);
    }

    public function wxOfficial()
    {
        $this->validate($this->getParams(), 'app\member\validate\AuthMember.LoginWxOfficial');

        $identity_type = $this->getParams('identity_type', false, 'wechat_official');

        $wx = new ThirdLogin();
        $wxResult = $wx->get_user_info($this->getParams('scope'), $this->getParams('code'), $identity_type);
        if ($wxResult['status'] == 2000) {
            return $this->sendResponse(ERROR_LOGIC, [], json_encode($wxResult['data']));
        }
        $user_info = $wxResult ['data'];

        $authMember = AuthMember::auth($this->identityType2Int($identity_type), $user_info['unionid'] ?? $user_info['openid'], '', $user_info['openid']);

        if (!$authMember) {
            //创建新用户
            $auth_data['auth_info'] = $user_info;
            $auth_data['identifier'] = $user_info['unionid'] ?? $user_info['openid'];
            $auth_data['open_id'] = $user_info['openid'];
            $auth_data['identity_type'] = $this->identityType2Int($identity_type);

            $member_data['status'] = 1;
            if ($this->getParams('scope') == 'snsapi_userinfo') {
                $member_data['nick_name'] = $user_info['nickname'];
                $member_data['gender'] = $user_info['sex'];
                $member_data['avatar'] = $user_info['headimgurl'];
            }

            $member = Member::register($member_data, $auth_data, $this->getParams('invitation_code'));
        } else {
            $member = $authMember->member;
        }

        $member->refresh();
        $res['token'] = $member->updateToken();
        $res['member'] = $member;
        return $this->sendResponse(SUCCESS, $res);
    }

    public function wxMp()
    {
        $code = $this->getParams('code', true);
        // 获取当前请求小程序信息
        $appid = '';
        $secret = '';
        $client_code = $this->request->header('CLIENT_CODE');
        if (!empty($client_code)) {
            $mp = Mp::where(['code' => $client_code])->find();
            if (!is_null($mp)) {
                $appid = $mp->app_id;
                $secret = $mp->app_secret;
            }
        }

        $mpLogin = new MpLogin();
        $user_info = $mpLogin->mp_code_verify($code, $appid, $secret);
        if(key_exists('errcode', $user_info)){
            return $this->sendResponse(ERROR_LOGIC, [], json_encode($user_info));
        }
        $authMember = AuthMember::auth($this->identityType2Int('wechat_mp'), $user_info['unionid'] ?? $user_info['openid'], '', $user_info['openid']);

        if (!$authMember) {
            //创建新用户
            $auth_data['auth_info'] = $user_info;
            $auth_data['identifier'] = $user_info['unionid'] ?? $user_info['openid'];
            $auth_data['open_id'] = $user_info['openid'];
            $auth_data['identity_type'] = $this->identityType2Int('wechat_mp');

            $member_data['status'] = 1;
            $member = Member::register($member_data, $auth_data, $this->getParams('invitation_code'));

            $message_array['application_name'] = saas_config('global.application_name');
            Message::sendMemberMessageByTemplate($member->member_id, "register_mp", $message_array);
        } else {
            $member = $authMember->member;
        }

        $member->refresh();
        $res['token'] = $member->updateToken();
        $res['member'] = $member;
        return $this->sendResponse(SUCCESS, $res);
    }

    private function identityType2Int(string $identity_type)
    {
        switch ($identity_type) {
            case 'mobile':
                return AuthMember::SAAS_AUTH_MOBILE;
            case 'wechat_official':
                return AuthMember::SAAS_AUTH_WX_OFFICIAL;
            case 'wechat_mp':
                return AuthMember::SAAS_AUTH_WX_MP;
            case 'wechat_web':
                return AuthMember::SAAS_AUTH_WX_PUBLIC;
        }
        return AuthMember::SAAS_AUTH_WX_OFFICIAL;
    }

    private function login(int $type, string $identifier, string $credential, string $openid = '')
    {
        $authMember = AuthMember::auth($type, $identifier, $credential, $openid);

        /** @var Member $member */
        if (is_null($authMember) || !$member = $authMember->member) {
            return $this->sendResponse(ERROR_LOGIC, [], '账号或者密码错误');
        }

        if ($member->status == 0) {
            return $this->sendResponse(ERROR_LOGIC, [], '账号已禁用');
        }

        $member->updateLoginStatus();

        $res['token'] = $member->updateToken();
        $res['member'] = $member;

        return $this->sendResponse(SUCCESS, $res);
    }

    public function forgetPassword()
    {
        $account = $this->getParams('account');
        $message_code = $this->getParams('message_code');
        //1.验证手机验证码
        if (SmsUtil::checkAuthMessage($account, $message_code) == false) {
            $this->sendResponse(ERROR_LOGIC, null, "error_message_code");
        }
        $member_map['mobile'] = $account;
        $mem = Member::where($member_map)->find();

        if (empty($mem)) {
            return $this->sendResponse(ERROR_LOGIC, [], "error_user_not_exist");
        } else {
            $data['credential'] = saas_password($this->getParams('password'));
            $model = AuthMember::where(['member_id' => $mem->member_id, 'identity_type' => 6])->find();
            $model->save($data);
            return $this->sendResponse(SUCCESS);
        }
    }
}
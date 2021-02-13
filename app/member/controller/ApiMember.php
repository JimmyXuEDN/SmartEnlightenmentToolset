<?php

namespace app\member\controller;

use app\base\controller\ApiBaseController;
use app\base\util\SmsUtil;
use app\conference\model\ConferenceMember;
use app\member\model\AuthMember;
use app\member\model\Member;
use app\message\model\Message;
use think\Request;
use wx\official\lib\api\User;

class ApiMember extends ApiBaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return $this->sendResponse(SUCCESS, Member::getList());
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        ConferenceMember::validate_status();
        // $member = Member::get($this->member['member_id']);
        if ($id == 0) {
            $id = $this->getMember()->member_id;
        }
        $member = Member::with(['mod'])->find($id);
        $member['statistics'] = $member->statistics();
        $member['rank'] = $member->rank();
        $member['integral_weekly'] = $member->integral_weekly();
        return $this->sendResponse(0, $member);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param \think\Request $request
     * @param int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $this->getMember()->save($this->getParams());
        return $this->sendResponse(SUCCESS);
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    /**
     * 用户绑定手机
     *
     * @return \think\Response
     */
    public function bindTel()
    {
        $account = $this->getParams('account');
        $message_code = $this->getParams('message_code');
        $password = $this->getParams('password');
        $member_id = $this->getMemberId();

        //1.验证手机验证码
        if (SmsUtil::checkAuthMessage($account, $message_code) == false) {
            return $this->sendResponse(ERROR_LOGIC, null, "error_message_code");
        }
        if (trim($password) == '' || trim($password) == null) {
            return $this->sendResponse(ERROR_LOGIC, null, "error_get_pass");
        }

        //验证成功,注册
        $map = [
            ['account', '=', $account],
            ['member_id', '<>', $member_id],
        ];
        $mem = Member::where($map)->find();
        if ($mem) {
            return $this->sendResponse(ERROR_LOGIC, null, "error_mobile_bind");
        } else {
            $data['account'] = $account;
            $data['password'] = saas_password($password);
            $res = Member::where('member_id', $member_id)->save($data);
            if ($res !== false) {
                //注册消息,使用模板发送
                $message_array['application_name'] = saas_config('global.application_name');
                $message_array['edit_time'] = date("Y-m-d H:i:s", time());
                Message::sendMemberMessageByTemplate($member_id, "bind_mobile", $message_array);

                return $this->sendResponse(SUCCESS);
            }
        }
    }


    /**
     * 用户修改手机
     *
     * @return \think\Response
     */
    public function editAccount()
    {
        $mobile = $this->getParams('mobile');
        $member_id = $this->getMemberId();

        $authMember = AuthMember::where(['member_id' => $member_id, 'identifier' => $mobile])->find();
        $data = $this->getParams();
        $data['identity_type'] = 1; // 认证类型
        $data['member_id'] = $member_id; // 认证类型
        if (is_null($authMember)) {
            AuthMember::create($data);
        } else {
            $authMember->save($data);
        }
        return $this->sendResponse(0);
    }

    /**
     * 用户修改密码
     *
     * @return \think\Response
     */
    public function editPassword()
    {
        $password = $this->getParams('password');
        if (saas_password($password) != $this->getMember()->password) {
            return $this->sendResponse(ERROR_LOGIC, null, "error_password");
        }

        $new_password = $this->getParams('new_password');
        $member_id = $this->getMemberId();

        //验证成功,注册
        $data['password'] = saas_password($new_password);;
        $map['member_id'] = $member_id;
        $res = Member::where($map)->update($data);
        if ($res !== false) {
            //注册消息,使用模板发送
            $message_array['application_name'] = saas_config('global.application_name');
            $message_array['edit_time'] = date("Y-m-d H:i:s", time());
            Message::sendMemberMessageByTemplate($member_id, "edit_pwd", $message_array);
            return $this->sendResponse(SUCCESS);
        }
    }

    public function getSonList()
    {
        $res = Member::getList($this->getMemberId());
        return $this->sendResponse(SUCCESS, $res);
    }

    public function subscribe()
    {
        $thirdlogin = $this->getMember()->authMember()->where('identity_type', 2)->find();
        if (isset($thirdlogin)) {
            $user = new User();
            $user_official = $user->getDetail($thirdlogin->open_id);
            $res['subscribe'] = $user_official['subscribe'];
        } else {
            $res['subscribe'] = 0;
        }
        return $this->sendResponse(SUCCESS, $res);
    }
}
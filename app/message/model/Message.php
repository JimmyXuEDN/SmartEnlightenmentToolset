<?php

namespace app\message\model;

use app\admin\model\AdminUser;
use app\base\model\BaseModel;
use app\base\model\GlobalModule;
use app\base\util\SmsUtil;
use app\member\model\Member;

class Message extends BaseModel
{

    protected $pk = 'message_id';

    public function messageMember()
    {
        return $this->hasMany(MessageMember::class, 'message_id', 'message_id');
    }

    public function messageUser()
    {
        return $this->hasMany(MessageUser::class, 'message_id', 'message_id');
    }

    /**
     * 使用模板消息发送
     * @param int $member_id
     * @param string $template_name
     * @param array $params
     * @param int $jump_type
     * @param string $jump_aim
     * @param int $send_type 0不使用, 1发送短信,2发送邮箱,3两个都发送
     */
    public static function sendMemberMessageByTemplate($member_id, $template_name, $params, $jump_type = 0, $jump_aim = '', $send_type = 0)
    {

        $message = MessageTemplate::createMessage($template_name, $params, $jump_type, $jump_aim);
        self::sendMemberMessage($message, $member_id, $send_type);
        if ($send_type !== 0 && saas_config('global.is_use_sms') == 1) {
            $member = Member::find($member_id);
            if (!empty($member->mobile)) {
                SmsUtil::getConferenceNoticeCode($member->mobile, $params['name'], $params['address'], $params['date']);
            }
        }
    }

    /**
     * 发送前台用户信息
     * @param Message $message (需要属性 title,detail,jump_type,jump_url,jump_id)
     * @param mixed $member_id 多个用户
     * @param int $send_type 0不使用, 1发送短信,2发送邮箱,3两个都发送
     */
    public static function sendMemberMessage(Message $message, $member_id, $send_type = 1)
    {
        if (!GlobalModule::checkModule("message")) {//没启用模块不生成信息
            return;
        }
        $message->save();
        $insert_array = array();
        if (is_array($member_id)) {
            foreach ($member_id as $id) {
                $insert_array[]['member_id'] = $id;
            }
            $message->messageMember()->saveAll($insert_array);
        } else {
            $message->messageMember()->save(['member_id' => $member_id]);
        }
    }

    /**
     * 使用模板消息发送
     * @param int $user_id
     * @param string $template_name
     * @param array $params
     * @param integer $jump_type
     * @param string $jump_aim
     * @param int $send_type 0不使用, 1发送短信,2发送邮箱,3两个都发送
     */
    public static function sendUserMessageByTemplate($user_id, $template_name, $params, $jump_type = 0, $jump_aim = '', $send_type = 0)
    {
        $message = MessageTemplate::createMessage($template_name, $params, $jump_type, $jump_aim);
        self::sendUserMessage($message, $user_id, $send_type);
    }

    /**
     * 发送后台用户信息
     * @param Message $message (需要属性 title,detail,jump_type,jump_url,jump_id)
     * @param mixed $user_id 多个用户
     * @param int $send_type 0不使用, 1发送短信,2发送邮箱,3两个都发送
     */
    public static function sendUserMessage(Message $message, $user_id, $send_type = 0)
    {
        if (!GlobalModule::checkModule("message")) {//没启用模块不生成信息
            return;
        }
        $message->save();
        $insert_array = array();
        if (is_array($user_id)) {
            foreach ($user_id as $id) {
                $insert_array[]['user_id'] = $id;
            }
            $message->messageUser()->saveAll($insert_array);
        } else {
            $message->messageUser()->save(['user_id' => $user_id]);
        }
    }

    /**
     * @param $template_name
     * @param $params
     * @param int $jump_type
     * @param string $jump_aim
     * @param int $send_type
     */
    public static function sendAllUserMessageByTemplate($template_name, $params, $jump_type = 0, $jump_aim = '', $send_type = 0)
    {
        $message = MessageTemplate::createMessage($template_name, $params, $jump_type, $jump_aim);
        $all_users = AdminUser::where(['role_id' => 0])->select();
        foreach ($all_users as $u) {
            self::sendUserMessage($message, $u->id, $send_type);
        }
    }
}
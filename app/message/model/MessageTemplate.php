<?php

namespace app\message\model;

use app\base\model\BaseModel;

class MessageTemplate extends BaseModel
{

    protected $pk = 'template_id';

    /**
     * 根据模板生成消息
     * @param string $template_name
     * @param array $params
     * @param int $jump_type
     * @param string $jump_aim
     * @return Message
     */
    public static function createMessage($template_name, $params, $jump_type = 0, $jump_aim = '')
    {
        $template = self::where(['template_name' => $template_name])->find();
        if (is_null($template)) {
            saas_abort(ERROR_SYSTEM, null, "system_error_lack_message_template", [$template_name]);
        }
        $message = new Message();
        $message->title = $template->title;
        $message->detail = $template->detail;
        $message->jump_type = $jump_type;
        $message->jump_aim = $jump_aim;

        if ($params) {
            foreach ($params as $key => $value) {
                $message->title = str_replace('{$' . $key . '}', $value, $message->title);
                $message->detail = str_replace('{$' . $key . '}', $value, $message->detail);
            }
        }
        return $message;
    }
}
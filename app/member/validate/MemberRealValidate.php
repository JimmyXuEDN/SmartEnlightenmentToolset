<?php
/**
 * Created by PhpStorm.
 * User: LucasXu
 * Date: 2021/2/20
 * Time: 16:27
 * Class MemberRealValidate
 * @package member
 */

namespace app\member\validate;

use app\base\validate\BaseValidate;

class MemberRealValidate extends BaseValidate
{
    protected $rule = [
        'member_id|对应用户' => 'require'
    ];
    protected $message = [];
}
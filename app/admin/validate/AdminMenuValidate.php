<?php
/**
 * Created by PhpStorm.
 * User: LucasXu
 * Date: 2021/2/19
 * Time: 21:10
 * Class AdminMenuValidate
 * @package admin
 */

namespace app\admin\validate;

use app\base\validate\BaseValidate;

class AdminMenuValidate extends BaseValidate
{
    protected $rule = [
        'name|名称' => 'require',
        'global_application_route_id|对应路由' => 'require'
    ];
    protected $message = [];
}
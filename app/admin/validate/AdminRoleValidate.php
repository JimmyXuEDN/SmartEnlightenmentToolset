<?php
/**
 * Created by PhpStorm.
 * User: LucasXu
 * Date: 2021/2/20
 * Time: 13:50
 * Class AdminRoleValidate
 * @package
 */

namespace app\admin\validate;

use app\base\validate\BaseValidate;

class AdminRoleValidate extends BaseValidate
{
    protected $rule = [
        'name|角色名' => 'require'
    ];
    protected $message = [];
}
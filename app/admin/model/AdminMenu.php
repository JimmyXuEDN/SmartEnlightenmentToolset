<?php
/**
 * Created by PhpStorm.
 * User: mzy
 * Date: 2020/3/22
 * Time: 13:58
 */

namespace app\admin\model;

use app\base\model\BaseModel;
use think\model\relation\BelongsTo;

/**
 * Class AdminMenuValidate
 * @package admin
 */
class AdminMenu extends BaseModel
{
    /**
     * 对应一个控制器
     * @return BelongsTo
     */
    public function route()
    {
        return $this->belongsTo('app\base\model\GlobalApplicationRoute');
    }
}
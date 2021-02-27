<?php
/**
 * Created by PhpStorm.
 * User: LucasXu
 * User: LucasXu
 * Date: 2021/2/15
 * Class GlobalApplication
 * @package base
 */

namespace app\base\model;

use think\model\relation\HasMany;

class GlobalApplication extends BaseModel
{
    /**
     * 有多个路由记录
     * @return HasMany
     */
    public function route()
    {
        return $this->hasMany('GlobalApplicationRoute');
    }
}
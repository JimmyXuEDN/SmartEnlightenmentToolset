<?php
/**
 * Created by PhpStorm.
 * User: LucasXu
 * Date: 2020/3/6
 * Time: 18:41
 * Class Cache
 * @package common
 */

namespace app\common\controller;

use app\BaseController;

class Cache extends BaseController
{
    /**
     * 清理缓存
     */
    public function clearCache()
    {
        \think\facade\Cache::clear();
        return json(saas_make_response(SUCCESS));
    }
}
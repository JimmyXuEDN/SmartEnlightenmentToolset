<?php
/**
 * Created by PhpStorm.
 * User: mzy
 * Date: 2020/3/21
 * Time: 18:10
 */

namespace app\common\controller;

use app\app\model\AppConfig;
use app\BaseController;
use app\base\model\GlobalConfig;
use think\Cache;

/**
 * Class Config
 * @package general
 */
class Config extends BaseController
{
    /**
     * 客户端配置
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function client()
    {
        $cache_data = Cache::get('config');
        if ($cache_data)
        {
            $res = json_decode($cache_data);
        } else {
            $res = GlobalConfig::where(['is_client' => 1])
                ->with(['type'])
                ->order('list_order DESC,create_time DESC')
                ->select();
            Cache::set('config', json_encode($res), 24 * 3600);
        }
        $this->sendResponse(0, $res);
    }
}
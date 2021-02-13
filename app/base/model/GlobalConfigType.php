<?php
/**
 * Created by PhpStorm.
 * User: mzy
 * Date: 2020/3/21
 * Time: 17:13
 */

namespace app\base\model;


use think\facade\Env;

/**
 * Class GlobalConfigType
 * @package general
 */
class GlobalConfigType extends BaseModel
{
    /**
     * @return \think\model\relation\HasMany
     */
    public function config()
    {
        return $this->hasMany(GlobalConfig::class, 'global_config_type_id', 'global_config_type_id');
    }

    /**
     * 加载配置
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function loadConfig()
    {
        $config_array = null;
        /**
         * 从环境变量中读取调试配置
         */
        $is_app_debug = Env::get('app_debug');
        if (!$is_app_debug) {
            $config_array = cache('system_config');
        }
        if (!$config_array) {
            $config_types = self::select();
            foreach ($config_types as $type) {
                $config_array[$type->type_name] = [];
                foreach ($type->config as $config) {
                    $config_array[$type->type_name][$config->config_name] = $config->config_val;
                }
            }

            cache('system_config', $config_array);
        }
        config($config_array);
    }
}
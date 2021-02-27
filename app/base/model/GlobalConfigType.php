<?php
/**
 * Created by PhpStorm.
 * User: mzy
 * Date: 2020/3/21
 * Time: 17:13
 */

namespace app\base\model;


use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Env;
use think\model\relation\HasMany;

/**
 * Class GlobalConfigType
 * @package general
 */
class GlobalConfigType extends BaseModel
{
    /**
     * @return HasMany
     */
    public function config()
    {
        return $this->hasMany(GlobalConfig::class);
    }

    /**
     * 加载配置
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
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
            $config_types = self::with(['config'])->select();
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
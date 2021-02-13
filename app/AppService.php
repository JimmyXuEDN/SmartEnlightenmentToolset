<?php
declare (strict_types = 1);

namespace app;

use think\facade\Route;
use think\Service;

/**
 * 应用服务类
 */
class AppService extends Service
{
    public function register()
    {
        // 服务注册
    }

    public function boot()
    {
        // 服务启动
        // 支持批量添加
        Route::pattern([
            'id'   => '\d+',
        ]);

    }
}

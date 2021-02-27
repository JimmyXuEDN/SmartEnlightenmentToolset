<?php
declare (strict_types = 1);

namespace app\base\middleware;

use app\base\model\GlobalApplication;
use app\base\model\GlobalApplicationRoute;
use app\base\model\GlobalModule;
use app\base\model\GlobalModuleController;
use app\Request;
use Closure;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Env;
use think\Response;

class RouteAuth
{
    private $except = [
        '/',
        '/common/test'
    ];

    /**
     * 判断模块开关
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function handle($request, Closure $next)
    {
        // 环境变量不检查访问权限
        if (!Env::get('app.check_access')) {
            return $next($request);
        }

        $url = $request->url();
        $method = $request->method();

        // 根据请求信息检查路由是否存在于路由表
        $record = GlobalApplicationRoute::routeExistCheck($url, $method);
        if ($record === false) {
            return json(saas_make_response(ERROR_ACCESS));
        }

        // 根据路由表Authorized检查用户登录
        $authorized = GlobalApplicationRoute::routeAuthorizedCheck($record);
        if ($authorized === false) {
            return json(saas_make_response(ERROR_TOKEN));
        }

        /**
         * TODO 用户权限
         */

        return $next($request);
    }
}

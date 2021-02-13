<?php
declare (strict_types = 1);

namespace app\base\middleware;

use app\base\model\GlobalModule;
use app\base\model\GlobalModuleController;
use think\Response;

class ModuleAuth
{
    private $except = [
        '/',
        '/common/test'
    ];
    /**
     * 判断模块开关
     * @param \app\Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        return $next($request);
        /**
         * TODO 用户权限
         */
//        foreach ($this->except as $except) {
//            if ($request->baseUrl() == $except) {
//                return $next($request);
//            }
//        }
//
//        /** @var GlobalModule $module */
//        $module = GlobalModule::where('module_name', app('http')->getName())->find();
//        if (!$module) {
//            saas_abort(ERROR_MODULE);
//        }
//
//        /** @var GlobalModuleController $moduleController */
//        $moduleController = $module->globalModuleControllers()->where('controller_name', $request->controller())->find();
//        trace('GlobalModule:' . GlobalModule::getLastSql(), 'debug');
//        $request->setModuleController($moduleController);
//
//        $request->setModule($module);
//        if (!$request->isModuleInstall() || !$request->isModuleAvailable()) {
//            saas_abort(ERROR_MODULE);
//        }
//
//        return $next($request);
    }
}

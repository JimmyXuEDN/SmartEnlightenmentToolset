<?php


namespace app\base\model;


class GlobalModule extends BaseModel
{
    /**
     * 一个应用对应多个控制器
     * @return \think\model\relation\HasMany
     */
    public function globalModuleControllers()
    {
        return $this->hasMany(GlobalModuleController::class, 'module_id', 'id');
    }

    /**
     * 应用检查
     * @param $module_name
     * @param string $controller_name
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function checkModule($module_name, $controller_name = '')
    {
        if ($controller_name) {
            return self::hasWhere('globalModuleControllers', ['controller_name'=>$controller_name])->where('module_name', $module_name)->find();
        }
        return self::where('module_name', $module_name)->find();
    }

    /**
     * 查看应用详情
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getDetail($id)
    {
        $module = self::find($id);
        $res['module'] = $module->toArray();
        $res['controller'] = $module->globalModuleControllers()->where('type', 0)->select();
        return $res;
    }

    /**
     * 获取目录
     * @param int $role_id
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getMenu($role_id = 1)
    {
        $map_module['is_menu_show'] = 1;//显示
        $map_module['is_installed'] = 1;//安装
        $map_module['is_available'] = 1;//启用
        $order['menu_order'] = 'DESC';
        $modules = self::where($map_module)->order($order)->select();
        foreach($modules as $k => $v) {
            $map_controller['is_menu_show'] = 1;
            $map_controller['type'] = 0; // 后台的菜单
            if ($role_id !== 1) { // 如果非超级管理员则判断controller的权限
                // 获取用户有权限的controller
                //$role_controller = model('AdminAccess')->where('rold_id', $role_id)->column('controller_id');
                // var_dump($role_controller);
            }
            $controllers = $v->globalModuleControllers()->where($map_controller)->order($order)->select();
            /* if (!$controllers) {
              unset($modules[$k]);
            } else {
              $modules[$k]['child'] = $controllers;
            } */
            $modules[$k]['child'] = $controllers;
        }
        return $modules;
    }

}

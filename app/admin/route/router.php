<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

// 后台模块
Route::resource('global/module', 'GlobalModule');
// 后台子级模块
Route::resource('global/module/controller', 'GlobalModuleController');
// 后台配置管理
Route::resource('config', 'AdminSystemConfigType');
// 后台用户RBAC
Route::resource('rbac', 'Rbac');
// 后台用户菜单
Route::resource('menu', 'Menu');
// 后台用户管理user
Route::resource('user', 'AdminUser');
// 后台用户密码管理
Route::resource('password', 'adminPassword');
// 后台角色管理role
Route::resource('role', 'adminRole');
// 权限管理access
Route::resource('access', 'AdminAccess');
// 后台系统信息
Route::resource('system', 'AdminSystem');

/**
 * 缺省路由
 */
Route::miss(function () {
    return json(saas_make_response(1003));
});

<?php

use think\facade\Route;

// 后台角色管理role
Route::resource('role', 'Role');
// 权限管理access
Route::resource('access', 'Access');
// 后台用户管理user
Route::resource('user', 'User');
// 后台用户菜单
Route::resource('menu', 'Menu');

/**
 * 缺省路由
 */
Route::miss(function () {
    return json(saas_make_response(1003));
});

<?php
use think\facade\Route;

Route::get('/', 'Index/index');

/**
 * 缺省路由
 */
Route::miss(function () {
    return json(saas_make_response(1003));
});

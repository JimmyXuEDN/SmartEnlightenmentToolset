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

Route::group('app', function () {
    Route::get('detail', 'ApiMember/detail');
    Route::post('update', 'ApiMember/update');
    Route::resource('real','ApiMemberReal');
    Route::resource('address', 'ApiMemberAddress');
});


Route::group('admin', function () {
    Route::resource('','AdminMember');
    Route::resource('real','AdminMemberReal');
});

/**
 * 缺省路由
 */
Route::miss(function () {
    return json(saas_make_response(1003));
});

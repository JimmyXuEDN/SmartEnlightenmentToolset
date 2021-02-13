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
    Route::post('login/mobile', 'ApiLogin/mobile');
    Route::post('login/account', 'ApiLogin/account');
    Route::get('check/login', 'ApiLogin/isLogin');
    Route::post('wx/official', 'ApiLogin/wxOfficial');
    Route::post('wx/mp', 'ApiLogin/wxMp');
    Route::post('forgetPassword','ApiLogin/forgetPassword');
});

Route::group('admin', function () {
    Route::post('login', 'AdminLogin/login');
    Route::resource('member','AdminAuthMember');
});

/**
 * 缺省路由
 */
Route::miss(function () {
    return json(saas_make_response(1003));
});

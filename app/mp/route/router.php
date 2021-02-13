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
    Route::resource('mp','ApiMp');
    Route::get('test/qr/:code','ApiMp/test_mp_qr');
    Route::post('pay/charge','ApiMpPay/charge');
    Route::post('pay/hook','ApiMpPay/hook');
});

Route::group('admin', function () {
    Route::resource('mp','AdminMp');
});

/**
 * 缺省路由
 */
Route::miss(function () {
    return json(saas_make_response(1003));
});

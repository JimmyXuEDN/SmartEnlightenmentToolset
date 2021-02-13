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
    Route::resource('member', 'ApiMember');
    Route::resource('search', 'ApiMemberSearch');
    Route::resource('address', 'ApiMemberAddress');
    Route::resource('bank', 'ApiMemberBank');
    Route::post('mobile', 'ApiMember/bindTel');
    Route::post('account', 'ApiMember/editAccount');
    Route::post('password', 'ApiMember/editPassword');
    Route::get('children', 'ApiMember/getSonList');
    Route::get('subscribe', 'ApiMember/subscribe');

    Route::resource('collection','ApiMemberCollection');
    Route::resource('feedback','ApiMemberFeedback');

    Route::resource('cart','ApiMemberCart');

    Route::resource('real','ApiMemberReal');

    Route::resource('conference','ApiMemberConference');
    Route::resource('mod','ApiMemberMod');
});


Route::group('admin', function () {
    Route::resource('member','AdminMember');
    Route::post('sort','AdminMember/memberSort');
    Route::resource('feedback','AdminMemberFeedback');
    Route::resource('real','AdminMemberReal');
    Route::resource('mod','AdminMemberMod');
    Route::post('street/statistics','AdminMember/streetStatistics');
});

/**
 * 缺省路由
 */
Route::miss(function () {
    return json(saas_make_response(1003));
});

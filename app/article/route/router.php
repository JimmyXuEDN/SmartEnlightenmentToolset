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
    Route::resource('article','ApiArticle');
    Route::resource('article/type','ApiArticleType');
});

Route::group('admin', function () {
    Route::resource('article','AdminArticle');
    Route::resource('article/type','AdminArticleType');
});

/**
 * 缺省路由
 */
Route::miss(function () {
    return json(saas_make_response(1003));
});

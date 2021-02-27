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

/*  ==========测试接口==========    */
Route::any('test', 'Test/index');
Route::post('test/rsa/encode', 'Test/rsa_encode');
/*  ==========测试接口END==========    */

/*  ==========公共接口==========    */
/*      =========缓存=========        */
Route::get('cache/clear', 'Common/cache/clearCache');
/*      =========缓存=========        */
/*      =========系统信息=========        */
Route::get('system', 'Common/system');
/*      =========系统信息END=========        */
/*      =========行业分类=========        */
Route::get('industry/category', 'Common/industry_category');
/*      =========行业分类END=========        */
/*      =========银行列表=========        */
Route::get('bank', 'Common/bank');
/*      =========银行列表END=========        */
/*      =========格言=========        */
Route::get('motto/one', 'Common/getOneRandomMotto');
/*      =========格言=========        */
/*  ==========公共接口END==========  */

Route::miss(function () {
    return json(saas_make_response(1003));
});

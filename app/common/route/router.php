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
Route::any('test/cache/clear', 'Test/clearCache');
Route::any('test', 'Test/index');
/*  ==========测试接口END==========    */

/*  ==========公共接口==========    */
/*      =========系统信息=========        */
Route::get('system', 'Common/system');
/*      =========模块=========        */
Route::get('module', 'Common/module');
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
/*      =========上传=========        */
Route::rule('upload/token', 'general/upload/token', 'GET');
Route::post('upload/local', 'general/upload/local');
Route::resource('upload', 'general/upload');
Route::get('upload/qiniuToken', 'general/upload/token');
Route::get('upload/oss/config', 'general/upload/oss_config');
/*      =========上传END=========        */
Route::get('common/config/client', 'general/config/client');
Route::post('common/wx/jsapisignature', 'general/common/getJsapiSignatrue');
Route::get('common/wx/openid/:code', 'general/common/getWxOpenid');
Route::post('common/express/info', 'general/common/getExpressInfo');
Route::get('common/shopname', 'general/common/getShopName');
/*      =========短信=========        */
Route::get('common/sms/:tel', 'general/common/sendSms');
Route::get('common/sms/:tel/:code', 'general/common/checkSms');
/*      =========短信END=========        */
/*      =========微信公众号=========        */
Route::post('common/wx/jsapisignature', 'general/common/getJsapiSignatrue');
Route::get('common/wx/openid/:code', 'general/common/getWxOpenid');
/*      =========微信公众号END=========        */
/*      =========物流=========        */
Route::post('common/express/info', 'general/common/getExpressInfo');
Route::get('common/logistics/explist', 'general/logistics/getExpList');//公司快递列表
Route::post('common/logistics/company', 'general/logistics/expCompanylist');//单号查询快递公司列表
Route::post('common/logistics/information', 'general/logistics/logisticsInfo');//单号查询快递公司列表
/*      =========物流END=========        */
/*      =========数据校验=========        */
Route::get('common/data/validate/member/tree', 'general/DataValidate/memberTree'); // 用户关系树校验
/*      =========数据校验END=========        */
/*      =========LBS地理信息=========        */
Route::get('common/lbs/amap/city', 'general/LbsByAMap/city');
Route::get('common/lbs/amap/poi', 'general/LbsByAMap/poi');
Route::post('common/lbs/amap/place/text', 'general/LbsByAMap/place_text');
Route::post('common/lbs/amap/place/around', 'general/LbsByAMap/place_around');
/*      =========LBS地理信息=========        */
/*      =========OSS STS=========        */
Route::get('common/oss/sts', 'general/Oss/getSts');
/*      =========OSS STS=========        */
/*      =========OSS IMM=========        */
Route::post('common/imm/convert', 'general/AliCloudImm/convertFileToImg');
/*      =========OSS IMM=========        */
/*      =========OSS OCR=========        */
Route::post('common/ocr/oss/api', 'general/AliCloudOcrApi/ocrOssImg');
Route::post('common/ocr/oss/green', 'general/AliCloudGreen/ocrOssGreenImg');
/*      =========OSS OCR=========        */
/*      =========其他=========        */
Route::get('common/motto/one', 'general/Common/getOneRandomMotto');
/*      =========其他=========        */
/*  ==========公共接口END==========  */

Route::miss(function () {
    return json(saas_make_response(1003));
});

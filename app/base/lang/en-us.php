<?php
return [
    //系统错误信息
    'success'=>"成功",
    'error_system'=>"系统错误 ",
    'error_params'=>"参数错误 ",
    'error_path'=>"路径错误 ",
    'error_sql'=>"数据库操作错误,缺少参数或者参数值不正确",
    'error_module'=>'模块不存在或者没有安装',
    'error_data_not_exist'=>"数据不存在",
    'error_data_repeat'=>"你操作得太快了",
    //  业务错误
    'error_logic'=>"",//提示性的业务错误都放在这里,通过$other_message来返回具体错误信息
    'error_token'=>"请登录",
    'error_merchant'=>'请先注册商户',
    'error_vip'=>'请先成为VIP',
    'NO_DATA' => '没有这个数据或者没有操作权限',
    // 核心框架错误
    'error_core'=>'核心框架错误',
    'error_config'=>'系统配置出现了异常',
    'error_user'=>'用户名错误',
    'error_password'=>'密码错误',
    'error_login'=>'用户名或者密码错误',

    //系统错误信息提示
    "system_error_lack_config"=>"系统没有配置变量%s,或变量没有加载(清除缓存)",
    "system_error_lack_message_template"=>"消息模板%s不存在",
    "system_error_lack_params"=>"%s参数不存在",

    //系统错误信息提示
    "order_error_lack_inventory"=>"'%s'商品库存不足",
    "order_error_no_sku"=>"id为%s的商品不存在",
    "order_error_no_address"=>"地址不存在",

    // 系统安全
    'error_sign' => '加密签名验证失败，禁止访问',

    //主题报名错误信息提示
    "subject_error_apply_null"=>"%s字段必填"
];
?>

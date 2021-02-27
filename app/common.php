<?php
// 应用公共文件
use app\base\exception\SaasException;
use pay\exception\PayException;
use pay\Pay;
use think\facade\Env;
use phpseclib\Crypt\RSA;
use think\facade\Lang;

define("SUCCESS", 0);//操作成功
define("ERROR_SYSTEM", 1000);// 系统错误
define("ERROR_PARAMS", 1001);// 参数错误
define("ERROR_SQL", 1002);// 数据库操作错误,缺少参数或者参数值不正确
define("ERROR_PATH", 1003);// 路径错误
define("ERROR_ACCESS", 1004);// 无权限
define("ERROR_LOGIC", 2000);// 逻辑错误,提示性错误
define("ERROR_TOKEN", 2001);// token已失效/无效,请重新登录
define("ERROR_PARAMS_CHECK", 2002);// 参数不符合验证规则
define("ERROR_SECURITY", 3000);// 安全验证
define("ERROR_SECURITY_SIGN_INVALID", 3001);// 签名无效
define("ERROR_PASSWORD_ENCRYPT_REQUIRE", 3002);

/**
 * 格式化数据输出
 * @param int $code 错误码
 * @param array $data 成功时返回的业务数据,错误时一般不返回
 * @param string $other_message 错误时需要返回的其它信息(需要多语言时为key)
 * @param array $massage_vars (多语言时如果有变量在这里传值)
 * @return array
 */
function saas_make_response($code, $data = [], $other_message = '', $massage_vars = [])
{
    $error_massage = Lang::get($code);

    if ($other_message) {
        $other_message = Lang::get($other_message);
    }

    if ($error_massage && !empty($other_message)) {
        $error_massage .= ',';
    }
    if (!empty($other_message)) {
        $error_massage .= $other_message;
    }

    $response = array(
        'timestamp' => time(),
        'returnCode' => $code,
        'message' => $error_massage
    );

    //检查没有数据的话返回null
    if ($data) {
        foreach ($data as $key => $value) {
            if (!$value && !is_numeric($value)) {
                $data[$key] = null;
            }
        }
        $responseData['responseData'] = $data;
    }
    $responseData['responseHeader'] = $response;

    return $responseData;
}

/**
 * 中断程序直接返回
 * @param $code
 * @param array $data
 * @param string $other_message
 * @param array $massage_vars
 * @throws SaasException
 */
function saas_abort($code, $data = [], $other_message = '', $massage_vars = [])
{
    $data = saas_make_response($code, $data, $other_message, $massage_vars);
    throw new SaasException($data);
}

/**
 * 构造分页结果
 * @param int $page
 * @param int $size
 * @param int $total
 * @return array
 */
function saas_response_page(int $page, int $size, int $total)
{
    $total_page = ceil($total / $size);
    return [
        'currentPage' => $page,
        'pageSize' => $size,
        'totalPage' => $total_page,
        'totalCount' => $total,
    ];
}

/**
 * 获取saas系统配置,没有设置时会抛出异常
 * @param string $key
 * @param bool $is_must
 * @return mixed
 * @throws SaasException
 */
function saas_config($key, $is_must = true)
{
    $value = config($key);
    if (is_null($value) && $is_must) {
        saas_abort(ERROR_SYSTEM, null, "system_error_lack_config", [$key]);
    }
    return $value;
}

/**
 * 返回加密后的密码
 * @param string $pass
 * @return string
 */
function saas_password(string $pass): string
{
    return substr(strtoupper(md5(md5($pass))), 2, 16);
}

/**
 * 管理用户密码规则
 * @param $value
 * @return string
 */
function saas_admin_password($value)
{
    return strtoupper(substr(md5(md5($value)), 5, 20));
}

/**
 * 快速初始化微信支付
 * @param null $config 具体key配置(一般不需要填)
 * @param null $notify_url 回调url
 * @param null $pay_type 支付类型:目前有JSAPI:公众号支付,NATIVE:扫码支付
 * @param null $openid 公众号支付时需要传openid
 * @throws PayException
 * @throws SaasException
 */
function saas_init_weixin_pay($config = null, $notify_url = null, $pay_type = null, $openid = null)
{
    if (is_null($config)) {
        $config = array(
            "type" => "wx",
            "app_id" => saas_config('wx_official.app_id'),
            "mch_id" => saas_config('wx_official.pay_mch'),
            "key" => saas_config('wx_official.pay_key'),
            "app_secret" => saas_config('wx_official.app_secret')
        );
    }
    if (!is_null($notify_url)) {
        $config['notify_url'] = $notify_url;
    }
    if (!is_null($pay_type)) {
        $config['trade_type'] = $pay_type;
    }
    if (!is_null($openid)) {
        $config['openid'] = $openid;
    }
    Pay::init($config);
}

/**
 * 快速初始化支付宝支付
 * @param null $config 具体key配置(一般不需要填)
 * @param null $notify_url 回调url
 * @param null $pay_type 支付类型:目前有wap:手机网页支付,web:电脑端网页支付,scan:扫码支付
 * @param null $return_url 前端支付成功跳转地址
 * @throws SaasException
 * @throws PayException
 */
function saas_init_ali_pay($config=null,$notify_url=null,$pay_type=null,$return_url=null){
    if(is_null($config)){
        $config=array(
            "type"=>"ali",
            "app_id"=>saas_config('ali_pay.app_id'),
            "client_private_key"=>saas_config('ali_pay.client_private_key'),
            "ali_public_key"=>saas_config('ali_pay.ali_public_key'),
            "notify_url"=>$notify_url,
            "return_url"=>$return_url,
            "trade_type"=>$pay_type
        );
    }
    Pay::init($config);
}

/**
 * 快速初始化银联支付
 * @param null $config 具体key配置(一般不需要填)
 * @param null $notify_url 回调url
 * @param null $pay_type 支付类型:目前有wap:手机网页支付,web:电脑端网页支付,scan:扫码支付
 * @param null $return_url 前端支付成功跳转地址
 * @throws PayException
 */
function saas_init_union_pay($config=null,$notify_url=null,$pay_type=null,$return_url=null){
    if(is_null($config)){
        $config=array(
            "type"=>"union",
            "mer_id"=>saas_config('union.mer_id'),
            "sign_pfx_path"=>saas_config('union.sign_pfx_path'),
            "sign_pfx_pass"=>saas_config('union.sign_pfx_pass'),
            "notify_url"=>$notify_url,
            "return_url"=>$return_url,
            "trade_type"=>$pay_type
        );
    }
    Pay::init($config);
}

/**
 * 根据时间戳返回这个月的第一天
 * @param number $timestmp
 * @return number
 */
function saas_get_first_day_of_month($timestmp=0){
    if($timestmp){
        return strtotime(date('Y-m-01 00:00:00', $timestmp));
    }
    return strtotime(date('Y-m-01 00:00:00', strtotime('now')));
}


/**
 * 根据时间戳返回这个月的最后一天(下个月1号0时0分0秒)
 * @param number $timestmp
 * @return number
 */
function saas_get_last_day_of_month($timestmp=0){
    if($timestmp){
        $firstday=date('Y-m-01 00:00:00', $timestmp);
        return strtotime("$firstday +1 month ");
    }
    $firstday=date('Y-m-01 00:00:00', strtotime('now'));
    return strtotime("$firstday +1 month ");
}


/**
 * 根据时间戳返回今天0点0分0秒
 * @param number $timestmp
 * @return number
 */
function saas_get_first_time_of_day($timestmp=0){
    if($timestmp){
        return strtotime(date('Y-m-d 00:00:00', $timestmp));
    }
    return strtotime(date('Y-m-d 00:00:00', strtotime('now')));
}


/**
 * 根据时间戳返回这一天最后一秒的时间(第二天0时0分0秒)
 * @param number $timestmp
 * @return number
 */
function saas_get_last_time_of_day($timestmp=0){
    if($timestmp){
        $firstday=date('Y-m-d 00:00:00', $timestmp);
        return strtotime("$firstday +1 day ");
    }
    $firstday=date('Y-m-d 00:00:00', strtotime('now'));
    return strtotime("$firstday +1 day ");
}

/**
 * 传入一个时间戳获得时间戳所在月份开始时间
 * @param $timestamp 目标时间戳
 * @param int $deviation 月份偏移量，后一个月+1，前一个-1
 * @return false|string
 */
function getTimeMonthFirstDay($timestamp, $deviation = 0) {
    return strtotime(date('Y-m-d 00:00:00', strtotime(date('Y-m-01', $timestamp) . ' ' . $deviation . ' month')));
}

/**
 * 根据传入的data和数量返回字符串
 * @param $data
 * @param int $length
 * @return string
 */
function getRandStringByData($data, $length = 1)
{
    $res = '';
    $count = count($data);
    for ($i = 0; $i < $length; $i++)
    {
        $num = mt_rand(0, $count - 1);
        $res .= $data[$num];
    }
    return $res;
}

/**
 * 指定天的周一和周天
 * @param $day
 * @return array
 */
function getdays($day)
{
    $lastday = date('Y-m-d', strtotime("$day Sunday"));
    $firstday = date('Y-m-d', strtotime("$lastday -6 days"));
    return array($firstday, $lastday);
}

/**
 * 指定月的第一天和最后一天
 * @param $day
 * @return array
 */
function getmonths($day)
{
    $firstday = date('Y-m-01', strtotime($day));
    $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
    return array($firstday, $lastday);
}

/**
 * 输入开始时间，结束时间，粒度（周，月，季度）
 * @param string $start 开始时间，如2020-10-04
 * @param string $end 结束时间，如2020-10-04
 * @param int $granularity 粒度 1日 2周 3月 4季
 * @param string $format 时间显示格式，默认md
 * @param string $split 时间周期分隔符
 * @return array
 */
function get_ld_times($start, $end, $granularity, $format = 'Y-m-d', $split = ',')
{
    $start = date($format, strtotime($start));
    $end = date($format, strtotime($end));
    $timeArr = [];
    if ($granularity === 1)
    {
        $st = strtotime($start);
        $et = strtotime($end);
        $interval = 24 * 60 * 60;
        for ($st;$st <= $et;$st+=$interval)
        {
            $timeArr[] = date($format, $st);
        }
    } else if ($granularity === 2) {
        $t1 = $start;
        $t2 = getdays($t1)['1'];
        while($t2 < $end || $t1 <= $end){//周为粒度的时间数组
            $timeArr[] = $t1 . $split . $t2;
            $t1 = date($format, strtotime("$t2 +1 day"));
            $t2 = getdays($t1)['1'];
            $t2 = $t2 > $end ? $end : $t2;
        }
    } else if ($granularity === 3) {
        $t1 = $start;
        $t2 = getmonths($t1)['1'];
        while ($t2 < $end || $t1 <= $end){//月为粒度的时间数组
            $timeArr[] = $t1.','.$t2;
            $t1 = date($format, strtotime("$t2 +1 day"));
            $t2 = getmonths($t1)['1'];
            $t2 = $t2 > $end ? $end : $t2;
        }
    } else if ($granularity === 4) {
        $tStr = explode('-',$start);
        $month = $tStr['1'];
        if($month <=3 ){
            $t2 = date("$tStr[0]-03-31");
        } else if ($month <= 6) {
            $t2 = date("$tStr[0]-06-30");
        } else if ($month <= 9) {
            $t2 = date("$tStr[0]-09-30");
        } else {
            $t2 = date("$tStr[0]-12-31");
        }
        $t1 = $start;
        $t2 = $t2 > $end ? $end : $t2;
        while ($t2 < $end || $t1 <= $end)
        {//月为粒度的时间数组
            $timeArr[] = $t1.','.$t2;
            $t1 = date($format, strtotime("$t2 +1 day"));
            $t2 = date($format, strtotime("$t1 +3 months -1 day"));
            $t2 = $t2 > $end ? $end : $t2;
        }
    }
    return $timeArr;
}

/**
 * 微信消息xml字段小写加下划线转大写
 * @param unknown $uncamelized_words
 * @return string
 */
function saas_wx_camelize($uncamelized_words)
{
    $uncamelized_words = str_replace("_", " ", strtolower($uncamelized_words));
    return trim(ucwords($uncamelized_words));
}

/**
 * 微信消息xml字段大写转小写加下划线.
 * 思路:
 * 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
 */
function saas_wx_uncamelize($camelCaps,$separator='_')
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
}


/**
 * 返回数字和字母的字符串
 * @param $length
 * @return string
 * @throws SaasException
 */
function saas_string($length){
    $parent_str = saas_config('global.random_string');
    $str_len = strlen($parent_str);
    $key = '';
    for($i=0;$i<$length;$i++)
    {
        $key .= $parent_str[mt_rand(0,$str_len-1)];    //生成php随机数
    }

    //判断取件码是否唯一
    $code_map['status'] = array('in',"0,2");//不是已取的订单
    $code_map['receive_code'] = $key;
    $m_o = db('CourierMerchantOrder')->where($code_map)->find();

    if($m_o){
        saas_string($length);
    }else{
        return $key;
    }
}

/**
 * 根据经纬度和半径计算出范围
 * @param string $lat 纬度
 * @param String $lng 经度
 * @param float $radius 半径 米
 * @return Array 范围数组
 */
function saas_get_location_scope($lat, $lng, $radius) {
    $degree = (24901*1609)/360.0;
    $dpmLat = 1/$degree;

    $radiusLat = $dpmLat*$radius;
    $minLat = $lat - $radiusLat;       // 最小纬度
    $maxLat = $lat + $radiusLat;       // 最大纬度

    $mpdLng = $degree*cos($lat * (3.14/180));
    $dpmLng = 1 / $mpdLng;
    $radiusLng = $dpmLng*$radius;
    $minLng = $lng - $radiusLng;      // 最小经度
    $maxLng = $lng + $radiusLng;      // 最大经度

    /** 返回范围数组 */
    $scope = array(
        'minLat'    =>  $minLat,
        'maxLat'    =>  $maxLat,
        'minLng'    =>  $minLng,
        'maxLng'    =>  $maxLng
    );
    return $scope;
}

/**
 * 获取两个经纬度之间的距离
 * @param  string $lat1 纬一
 * @param  String $lng1 经一
 * @param  String $lat2 纬二
 * @param  String $lng2 经二
 * @return float  返回两点之间的距离 米
 */
function saas_get_location_distance($lat1, $lng1, $lat2, $lng2) {
    /** 转换数据类型为 double */
    $lat1 = doubleval($lat1);
    $lng1 = doubleval($lng1);
    $lat2 = doubleval($lat2);
    $lng2 = doubleval($lng2);
    /** 以下算法是 Google 出来的，与大多数经纬度计算工具结果一致 */
    $theta = $lng1 - $lng2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    return ($miles * 1.609344)*1000;
}

/**
 * 获取环境变量RSA public key
 * @return string
 */
function saas_get_env_public_key()
{
    return "-----BEGIN PUBLIC KEY-----\n" . Env::get('app.rsa_public_key') . "\n-----END PUBLIC KEY-----";
}

/**
 * 获取环境变量的RSA private key
 * @return string
 */
function saas_get_env_private_key()
{
    return "-----BEGIN RSA PRIVATE KEY-----\n" . Env::get('app.rsa_private_key') . "\n-----END RSA PRIVATE KEY-----";
}

/**
 * RSA公钥加密
 * @param $string
 * @return string
 */
function saas_rsa_encode($string)
{
    $public_key = saas_get_env_public_key();
    openssl_public_encrypt($string, $data, $public_key);
    return base64_encode($data);
}

/**
 * RSA私钥解密
 * @param $data
 * @return string
 * @throws SaasException
 */
function saas_rsa_decode($data)
{
    $private_key = saas_get_env_private_key();
    openssl_private_decrypt(base64_decode($data),$decrypt_data,$private_key);
    $data = explode(Env::get('app.rsa_key') . '_', $decrypt_data);
    $ret = null;
    if (isset($data[1]))
    {
        $ret = $data[1];
    }
    return $ret;
}

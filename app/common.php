<?php
// 应用公共文件
use app\base\exception\SaasException;

define("SUCCESS", 0);//操作成功
define("ERROR_SYSTEM", 1000);//系统错误
define("ERROR_PARAMS", 1001);//参数错误
define("ERROR_SQL", 1002);//数据库操作错误,缺少参数或者参数值不正确
define("ERROR_PATH", 1003);//路径错误
define("ERROR_LOGIC", 2000);//逻辑错误,提示性错误
define("ERROR_TOKEN", 2001);//token已失效/无效,请重新登录
define("ERROR_SECURITY", 3000);//安全验证
define("ERROR_SECURITY_SIGN_INVALID", 3001);//签名无效

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
    $error_massage = lang(config('error_code')[$code]);
    if ($other_message) {
        $other_message = lang($other_message, $massage_vars);
    }
    if (!empty($error_massage) && !empty($other_message)) {
        $error_massage = $error_massage . '' . $other_message;
    } else {
        $error_massage = $error_massage . $other_message;
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

function saas_admin_password($value)
{
    return strtoupper(substr(md5(md5($value)), 5, 20));
}

function saas_is_tel($tel)
{
    $strRule = "/^[1]\d{10}$/";
    if (preg_match($strRule, $tel) == 0) {
        return false;
    }
    return true;
}

/**
 * 计算邮费
 * @param int $num 商品数量
 * @param double $price 商品价格
 * @return int|mixed
 * @throws SaasException
 */
function saas_count_postage($num, $price)
{
    $demand_price = saas_config('postage.demand_price');
    $demand_num = saas_config('postage.demand_num');
    $postage = saas_config('postage.postage_price');
    if ($demand_price > 0 && $price >= $demand_price) {
        return 0;
    }

    if ($demand_num > 0 && $num >= $demand_num) {
        return 0;
    }
    return $postage;
}

/**
 * 快速初始化微信支付
 * @param null $config 具体key配置(一般不需要填)
 * @param null $notify_url 回调url
 * @param null $pay_type 支付类型:目前有JSAPI:公众号支付,NATIVE:扫码支付
 * @param null $openid 公众号支付时需要传openid
 * @throws \pay\exception\PayException
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

    \pay\Pay::init($config);

}

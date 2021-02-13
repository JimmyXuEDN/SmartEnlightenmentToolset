<?php

namespace pay\driver;

use com\unionpay\acp\sdk\AcpService;
use com\unionpay\acp\sdk\SDKConfig;
use pay\data\Charge;
use pay\data\Order;
use pay\exception\PayException;

class Union extends Base
{

    protected $trade_type;//支付方式:web,wap,app
    protected $acp_client;

    public function __construct($config = [])
    {
        require_once root_path('extend/pay/lib/union/autoload.php');
        $this->config = $config;
        SDKConfig::getSDKConfig()->logLevel = "INFO";
        SDKConfig::getSDKConfig()->logFilePath = EXTEND_PATH . "pay" . DS . "lib" . DS . "union" . DS . "log" . DS;

        SDKConfig::getSDKConfig()->frontUrl = $this->getConfig("return_url");
        SDKConfig::getSDKConfig()->backUrl = $this->getConfig("notify_url");

        SDKConfig::getSDKConfig()->signCertPath = $this->getConfig("sign_pfx_path");
        SDKConfig::getSDKConfig()->signCertPwd = $this->getConfig("sign_pfx_pass");

//        SDKConfig::getSDKConfig()->encryptCertPath = EXTEND_PATH ."pay".DS."lib".DS."union".DS."cert".DS."acp_test_enc.cer";
//        SDKConfig::getSDKConfig()->rootCertPath = EXTEND_PATH ."pay".DS."lib".DS."union".DS."cert".DS."acp_test_root.cer";
//        SDKConfig::getSDKConfig()->middleCertPath =  EXTEND_PATH ."pay".DS."lib".DS."union".DS."cert".DS."acp_test_middle.cer";

        SDKConfig::getSDKConfig()->encryptCertPath = EXTEND_PATH . "pay" . DS . "lib" . DS . "union" . DS . "cert" . DS . "acp_prod_enc.cer";
        SDKConfig::getSDKConfig()->rootCertPath = EXTEND_PATH . "pay" . DS . "lib" . DS . "union" . DS . "cert" . DS . "acp_prod_root.cer";
        SDKConfig::getSDKConfig()->middleCertPath = EXTEND_PATH . "pay" . DS . "lib" . DS . "union" . DS . "cert" . DS . "acp_prod_middle.cer";
        $this->trade_type = $this->getConfig('trade_type', false);

    }


    public function unifiedOrder(Order $order)
    {


        switch ($this->trade_type) {
            case 'wap':
            case 'web':
                $params = array(

                    //以下信息非特殊情况不需要改动
                    'version' => SDKConfig::getSDKConfig()->version,                 //版本号
                    'encoding' => 'utf-8',                  //编码方式
                    'txnType' => '01',                      //交易类型
                    'txnSubType' => '01',                  //交易子类
                    'bizType' => '000201',                  //业务类型
                    'frontUrl' => SDKConfig::getSDKConfig()->frontUrl,  //前台通知地址
                    'backUrl' => SDKConfig::getSDKConfig()->backUrl,      //后台通知地址
                    'signMethod' => SDKConfig::getSDKConfig()->signMethod,                  //签名方法
                    'channelType' => '08',                  //渠道类型，07-PC，08-手机
                    'accessType' => '0',                  //接入类型
                    'currencyCode' => '156',              //交易币种，境内商户固定156

                    //TODO 以下信息需要填写
                    'merId' => $this->getConfig("mer_id"),        //商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
                    'orderId' => time() . $order->order_id,    //商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
                    'txnTime' => date('YmdHis', time()),    //订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
                    'txnAmt' => $order->price * 100,    //交易金额，单位分，此处默认取demo演示页面传递的参数

                    // 订单超时时间。
                    // 超过此时间后，除网银交易外，其他交易银联系统会拒绝受理，提示超时。 跳转银行网银交易如果超时后交易成功，会自动退款，大约5个工作日金额返还到持卡人账户。
                    // 此时间建议取支付时的北京时间加15分钟。
                    // 超过超时时间调查询接口应答origRespCode不是A6或者00的就可以判断为失败。
                    'payTimeout' => date('YmdHis', strtotime('+30 minutes')),

                    'riskRateInfo' => $order->detail,

                    // 请求方保留域，
                    // 透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据。
                    // 出现部分特殊字符时可能影响解析，请按下面建议的方式填写：
                    // 1. 如果能确定内容不会出现&={}[]"'等符号时，可以直接填写数据，建议的方法如下。
                    //    'reqReserved' =>'透传信息1|透传信息2|透传信息3',
                    // 2. 内容可能出现&={}[]"'符号时：
                    // 1) 如果需要对账文件里能显示，可将字符替换成全角＆＝｛｝【】“‘字符（自己写代码，此处不演示）；
                    // 2) 如果对账文件没有显示要求，可做一下base64（如下）。
                    //    注意控制数据长度，实际传输的数据长度不能超过1024位。
                    //    查询、通知等接口解析时使用base64_decode解base64后再对数据做后续解析。
                    //    'reqReserved' => base64_encode('任意格式的信息都可以'),

                    //TODO 其他特殊用法请查看 special_use_purchase.php
                );
                AcpService::sign($params);
                $uri = SDKConfig::getSDKConfig()->frontTransUrl;
                $html_form = AcpService::createAutoFormHtml($params, $uri);
                return $html_form;
                break;
            case 'scan':
                $params = array(

                    //以下信息非特殊情况不需要改动
                    'version' => SDKConfig::getSDKConfig()->version,                 //版本号
                    'encoding' => 'utf-8',                  //编码方式
                    'txnType' => '01',                      //交易类型
                    'txnSubType' => '07',                  //交易子类
                    'bizType' => '000000',                  //业务类型
                    'backUrl' => SDKConfig::getSDKConfig()->backUrl,      //后台通知地址
                    'signMethod' => SDKConfig::getSDKConfig()->signMethod,                  //签名方法
                    'channelType' => '08',                  //渠道类型，07-PC，08-手机
                    'accessType' => '0',                  //接入类型
                    'currencyCode' => '156',              //交易币种，境内商户固定156

                    //TODO 以下信息需要填写
                    'merId' => $this->getConfig("mer_id"),        //商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
                    'orderId' => time() . $order->order_id,    //商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
                    'txnTime' => date('YmdHis', time()),    //订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
                    'txnAmt' => $order->price * 100,    //交易金额，单位分，此处默认取demo演示页面传递的参数

                    // 请求方保留域，
                    // 透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据。
                    // 出现部分特殊字符时可能影响解析，请按下面建议的方式填写：
                    // 1. 如果能确定内容不会出现&={}[]"'等符号时，可以直接填写数据，建议的方法如下。
                    //    'reqReserved' =>'透传信息1|透传信息2|透传信息3',
                    // 2. 内容可能出现&={}[]"'符号时：
                    // 1) 如果需要对账文件里能显示，可将字符替换成全角＆＝｛｝【】“‘字符（自己写代码，此处不演示）；
                    // 2) 如果对账文件没有显示要求，可做一下base64（如下）。
                    //    注意控制数据长度，实际传输的数据长度不能超过1024位。
                    //    查询、通知等接口解析时使用base64_decode解base64后再对数据做后续解析。
                    //    'reqReserved' => base64_encode('任意格式的信息都可以'),

                    //TODO 其他特殊用法请查看 pages/api_16_qrc/special_use_purchase.php
                );
                AcpService::sign($params); // 签名
                $url = SDKConfig::getSDKConfig()->backTransUrl;

                $result_arr = AcpService::post($params, $url);
                if (count($result_arr) <= 0) { //没收到200应答的情况
                    throw new PayException("没收到200应答");
                }

                if (!AcpService::validate($result_arr)) {
                    throw new PayException("验签失败:");
                }

                if ($result_arr["respCode"] != "00") {
                    throw new PayException("交易失败:" . $result_arr["respMsg"]);
                }
                return $result_arr['qrCode'];
                break;
        }

    }


    public function payHook($input)
    {
        $var = AcpService::validate($input);
        if (!$var) {
            exit('success');
        }
        if ($input['respCode'] != '00' || $input['respCode'] != 'A6') {
            exit('success');
        }
        $charge = new Charge();
        $charge->type = "union";
        $charge->channel = "union";
        $charge->order_id = substr($input['orderId'], 10);
        $charge->price = $input['settleAmt'];
        $charge->attach = "";
        $charge->transaction_no = $input['traceNo'];
        $charge->pay_time = strtotime($input['traceTime']);;

        return $charge;
    }


    public function payHookReply($is_success = true)
    {
        if ($is_success) {
            exit('success');
        } else {
            exit('error');
        }
    }
}
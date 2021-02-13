<?php

namespace pay\driver;

use pay\data\Order;
use pay\data\Charge;
use pay\exception\PayException;

class Ali extends Base
{

    protected $trade_type;//支付方式:web,wap,app
    protected $aop_client;

    public function __construct($config = [])
    {
        require_once root_path('extend/pay/lib/ali/lib/autoload.php');
        $this->config = $config;
        $this->aop_client = new \AopClient();
        $this->aop_client->appId = $this->getConfig('app_id');
        $this->aop_client->rsaPrivateKey = $this->getConfig('client_private_key');
        $this->aop_client->alipayrsaPublicKey = $this->getConfig('ali_public_key');

        $this->trade_type = $this->getConfig('trade_type', false);

    }


    public function unifiedOrder(Order $order)
    {

        $biz_content['subject'] = $order->title;
        $biz_content['body'] = $order->detail;
        $biz_content['timeout_express'] = "30m";
        $biz_content['out_trade_no'] = $order->order_id . "";
        $biz_content['total_amount'] = $order->price . "";
        switch ($this->trade_type) {
            case 'wap':
                $biz_content['product_code'] = "QUICK_WAP_WAY";
                $request = new \AlipayTradeWapPayRequest();
                $request->setNotifyUrl($this->getConfig("notify_url"));
                $request->setReturnUrl($this->getConfig("return_url"));
                $request->setBizContent(json_encode($biz_content, JSON_UNESCAPED_UNICODE));
                $res = $this->aop_client->pageExecute($request);
                return $res;
                break;
            case 'web':
                $biz_content['product_code'] = "FAST_INSTANT_TRADE_PAY";
                $request = new \AlipayTradePagePayRequest();
                $request->setNotifyUrl($this->getConfig("notify_url"));
                $request->setReturnUrl($this->getConfig("return_url"));
                $request->setBizContent(json_encode($biz_content, JSON_UNESCAPED_UNICODE));
                $res = $this->aop_client->pageExecute($request);
                return $res;
                break;
            case 'scan':
                $biz_content['product_code'] = "FACE_TO_FACE_PAYMENT";
                $request = new \AlipayTradePrecreateRequest();
                $request->setNotifyUrl($this->getConfig("notify_url"));
                $request->setBizContent(json_encode($biz_content, JSON_UNESCAPED_UNICODE));
                $res = $this->aop_client->execute($request);
                if ($res->alipay_trade_precreate_response->code != '10000') {
                    throw new PayException(json_encode($res, JSON_UNESCAPED_UNICODE));
                }
                return $res->alipay_trade_precreate_response->qr_code;
                break;
        }


    }


    public function payHook($input)
    {
        $var = $this->aop_client->rsaCheckV1($input, null, "RSA2");
        if (!$var) {
            exit('success');
        }
        if ($input['trade_status'] != 'TRADE_SUCCESS') {
            exit('success');
        }
        $charge = new Charge();
        $charge->type = "ali";
        $charge->channel = "ali";
        $charge->order_id = $input['out_trade_no'];
        $charge->price = $input['total_amount'];
        $charge->attach = "";
        $charge->transaction_no = $input['trade_no'];
        $charge->pay_time = strtotime($input['gmt_payment']);;

        return $charge;
    }


    public function payHookReply($is_success = true)
    {
        $reply = new \WxPayNotifyReply();
        if ($is_success) {
            exit('success');
        } else {
            exit('error');
        }
    }
}
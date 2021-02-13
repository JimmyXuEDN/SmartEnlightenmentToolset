<?php

namespace pay\driver;

use pay\data\Order;
use pay\data\TransferBank;
use pay\data\TransferPurse;
use \pay\exception\PayException;
use pay\data\Charge;
use pay\Util;

class Wx extends Base
{

    protected $trade_type;

    public function __construct($config = [])
    {
        require_once root_path('extend/pay/lib/wx/autoload.php');
        $this->config = $config;
        \WxPayConfig::$APPID = $this->getConfig('app_id');
        \WxPayConfig::$MCHID = $this->getConfig('mch_id');
        \WxPayConfig::$KEY = $this->getConfig('key');
        \WxPayConfig::$SSLCERT_PATH = EXTEND_PATH . "pay" . DS . "lib" . DS . "wx" . DS . "cert" . DS . "apiclient_cert.pem";
        \WxPayConfig::$SSLKEY_PATH = EXTEND_PATH . "pay" . DS . "lib" . DS . "wx" . DS . "cert" . DS . "apiclient_key.pem";
        \WxPayConfig::$KEY = $this->getConfig('key');
        if ($this->getConfig('app_secret', false)) {
            \WxPayConfig::$APPSECRET = $this->getConfig('app_secret');
        }
        if ($this->getConfig('cert', false)) {
            \WxPayConfig::$SSLCERT_PATH = $this->getConfig('cert');
        }
        if ($this->getConfig('cert_key', false)) {
            \WxPayConfig::$SSLKEY_PATH = $this->getConfig('cert_key');
        }

        if ($this->getConfig('trade_type', false)) {
            $this->trade_type = $this->getConfig('trade_type');
        }

    }


    public function unifiedOrder(Order $order)
    {
        $wx_order = new \WxPayUnifiedOrder();
        $wx_order->SetOut_trade_no($order->order_id);
        $wx_order->SetBody($order->title);
        $wx_order->SetDetail($order->detail);
        $wx_order->SetAttach($order->attach);
        $wx_order->SetTotal_fee($order->price * 100);
        $wx_order->SetTrade_type($this->trade_type);
        $notify_url = $this->getConfig('notify_url');
        $wx_order->SetNotify_url($notify_url);
        if ($this->getConfig('openid', false)) {
            $wx_order->SetOpenid($this->getConfig('openid'));
        }
        if ($this->trade_type == 'NATIVE') {
            $wx_order->SetProduct_id($order->order_id);
        }

        $result = \WxPayApi::unifiedOrder($wx_order);
        if ($result['return_code'] != 'SUCCESS') {
            throw new PayException(json_encode($result));
        }
        if ($result['result_code'] != 'SUCCESS') {
            throw new PayException("交易失败:错误代码为" . $result['err_code']);
        }

        if ($this->trade_type == 'JSAPI') {
            //公众号支付
            $pay_params['appId'] = $result['appid'];
            $pay_params['nonceStr'] = $result['nonce_str'];
            $pay_params['signType'] = "MD5";
            $pay_params['timeStamp'] = time() . "";
            $pay_params['package'] = "prepay_id=" . $result['prepay_id'];
            //var_dump($pay_params);exit;

            $app_params = new \AppPayParams();
            $app_params->FromArray($pay_params);
            $app_params->SetSign();

            $sing_array = $app_params->getArray();
            $sing_array['paySign'] = $sing_array['sign'];
            unset($sing_array['sign']);

            return $sing_array;
        }

        if ($this->trade_type == 'NATIVE') {
            //二维码支付
            return $result['code_url'];
        }
    }


    public function payHook($input)
    {
        $result = \WxPayResults::Init($input);
        $charge = new Charge();
        $charge->type = "wx";
        $charge->channel = $result['trade_type'] == "APP" ? "app" : "h5";
        $charge->order_id = $result['out_trade_no'];
        $charge->price = $result['total_fee'] / 100;
        $charge->attach = isset($result['attach']) ? $result['attach'] : "";
        $charge->transaction_no = $result['transaction_id'];
        $charge->pay_time = strtotime($result['time_end']);
        return $charge;
    }

    public function payWxOfficialHook($input)
    {
        return \WxPayResults::Init($input);
    }

    public function payMpHook($input)
    {
        return \WxPayResults::Init($input);
    }

    public function payHookReply($is_success = true)
    {
        $reply = new \WxPayNotifyReply();
        if ($is_success) {
            $reply->SetReturn_code("SUCCESS");
            $reply->SetReturn_msg("OK");
            $reply->SetSign();
        } else {
            $reply->SetReturn_code("FAIL");
            $reply->SetReturn_msg("ERROR");
        }
        exit($reply->ToXml());
    }

    /**
     * 获取付款到银行中需要的公钥
     */
    public function getTransferBankPublicKey()
    {
        $result = \WxPayApi::getPublicKey();
        if ($result['return_code'] != 'SUCCESS') {
            throw new PayException(json_encode($result));
        }
        if ($result['result_code'] != 'SUCCESS') {
            throw new PayException("交易失败:错误代码为" . $result['err_code']);
        }
        return $result['pub_key'];
    }

    /**
     * @param TransferBank $transferBank
     * @return \成功时返回，其他抛异常
     * @throws PayException
     * @throws \WxPayException
     */
    public function transferToBank($transferBank)
    {
        $wx_transfer_bank = new \WxTransferBank();
        $wx_transfer_bank->SetTrade_no($transferBank->order_id);
        $wx_transfer_bank->SetAmount($transferBank->price);
        $wx_transfer_bank->SetBank_code($transferBank->bank_code);
        $wx_transfer_bank->SetBank_no(Util::wx_rsa_encrypt($transferBank->bank_no));
        $wx_transfer_bank->SetTrue_name(Util::wx_rsa_encrypt($transferBank->real_name));
        $wx_transfer_bank->SetDesc($transferBank->remark);

        $result = \WxPayApi::transferToBank($wx_transfer_bank);
        if ($result['return_code'] != 'SUCCESS') {
            throw new PayException(json_encode($result, JSON_UNESCAPED_UNICODE));
        }
        if ($result['result_code'] != 'SUCCESS') {
            throw new PayException("交易失败:错误代码为" . json_encode($result, JSON_UNESCAPED_UNICODE));
        }
        return $result;
    }

    /**
     * 支付到钱包
     * @param TransferPurse $transferPurse
     * @return \成功时返回，其他抛异常
     */
    public function transferToPurse($transferPurse)
    {
        $wx_transfer_purse = new \WxTransferPurse();
        $wx_transfer_purse->SetTrade_no($transferPurse->order_id);
        $wx_transfer_purse->SetAmount($transferPurse->price);
        $wx_transfer_purse->SetOpenid($transferPurse->openid);
        $wx_transfer_purse->SetTrue_name($transferPurse->real_name);
        $wx_transfer_purse->SetDesc($transferPurse->remark);

        $result = \WxPayApi::transferToPurse($wx_transfer_purse);
        if ($result['return_code'] != 'SUCCESS') {
            throw new PayException(json_encode($result, JSON_UNESCAPED_UNICODE));
        }
        if ($result['result_code'] != 'SUCCESS') {
            throw new PayException("交易失败:错误代码为" . json_encode($result, JSON_UNESCAPED_UNICODE));
        }
        return $result;
    }
}
<?php

namespace app\mp\controller;

use app\agency\model\Agency;
use app\base\controller\ApiBaseController;
use app\mp\model\Mp;
use app\order\model\OrderData;
use pay\data\Order;
use pay\Pay;

class ApiMpPay extends ApiBaseController
{
    const GOOD_ORDER = 1;

    /**
     * 小程序支付
     * 生成charge
     */
    public function charge()
    {
        $openid = $this->getParams('openid', true);
        $mp_code = $this->getParams('mp_code', true);
        $mp = Mp::where('code', $mp_code)->find();
        if (is_null($mp)) {
            return $this->sendResponse(2000, [], lang('mp_null'));
        }
        $type = $this->getParams('type', true);
        $order_data = null;
        switch ($type){
            case 1:
                $order_id = $this->getParams('order_id', true);
                $order_data_object = new OrderData();
                $order_data = $order_data_object->orderPayCharge($order_id);
                break;
            case 2:
                // $agency = $member->agency();
                $agency = Agency::where(['member_id' => $this->getMember()->member_id])->find();
                if (is_null($agency)) {
                    return $this->sendResponse(ERROR_LOGIC, null, lang('error_agency_not_exist') . saas_config("distribution.agency_name"));
                }
                if(!empty($agency->charge)){
                    return $this->sendResponse(ERROR_LOGIC, null, lang('error_agency_paid'));
                }
                $order_data = $agency->orderPayCharge();
                break;
            default:
                $this->sendResponse(2000, [], lang('MP_INVALID_PAY_TYPE'));
        }

        if (is_null($order_data)) {
            $this->sendResponse(2000, [], lang('MP_ORDER_NULL'));
        }

        $order = new Order();
        $order->title = $order_data->title;
        $order->order_id = $order_data->order_id . time();
        $order->detail = $order_data->detail;
        $order->price = $order_data->price;

        /**
         * 自定义参数
         * type 订单类型
         * mp_code 小程序code
         * order_id 订单号
         */
        $attach = [
            'type' => $type,
            'mp_code' => $mp_code,
            'order_id' => $order_data->order_id,
        ];
        $order->attach = json_encode($attach);
        $config = array(
            "app_id" => $mp->app_id,
            "mch_id" => $mp->mc_id,
            "key" => $mp->pay_secret_key,
            "app_secret" => $mp->app_secret
        );
        saas_init_weixin_pay($config,"https://{$_SERVER['HTTP_HOST']}/app/mp/pay/hook","JSAPI", $openid);
        $res['charge']=Pay::unifiedOrder($order);
        return $this->sendResponse(SUCCESS,$res);
    }

    public function hook()
    {
        saas_init_weixin_pay();
        $result = Pay::payMpHook($this->request->getContent());
        $attach = json_decode($result['attach'], true);

        /**
         * 处理回调业务逻辑
         */
        if (isset($attach)) {
            switch (intval($attach['type'])){
                case 1:
                    $order_data = new OrderData();
                    $result['pay_type'] = 4;
                    $result['result'] = $result;
                    $order_data->orderPayProcess($result);
                    break;
                case 2:
                    $agency = new Agency();
                    $agency->payHook($result);
            }
        }

        Pay::payHookReply(true);
    }
}
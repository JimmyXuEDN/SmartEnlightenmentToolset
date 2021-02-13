<?php
/**
 * 支付回调
 */
namespace app\general\controller;
use app\common\controller\ApiBaseUnsign;
use app\groupbuy\model\GroupbuyOrder;
use app\merchant\model\MerchantReceiveOrder;
use app\order\model\OrderDataDetail;
use app\package\model\PackageOrder;
use \think\Db;
use pay\Pay;
use pay\data\Order;
use pay\data\Charge;
use app\message\model\Message;
use app\registration\model\RegistrationOperate;
use app\subject\model\SubjectApply;
use app\subject\model\SubjectApplyForm;
use app\order\model\OrderData;
use app\agency\model\Agency;
use app\member\model\Member;
use app\agency\model\AgencyOrderBack;
use app\vip\model\VipPackageOrder;
use app\integral\model\IntegralOrder;
use app\integral\model\IntegralRecord;

class PayHook extends ApiBaseUnsign {

    public function _initialize()
    {
        parent::_initialize();
    }

    //积分回调
    public function integralHook(){
        saas_init_weixin_pay();
        //参考/pay/data/Charge
        $charge=Pay::payHook($this->request->getContent());

        //业务处理
        $charge_array = $charge->getArray();
        $map['id'] = substr_replace($charge_array['order_id'],'',0,3);
        $integral = db('IntegralRecord')->where($map)->find();
        if($integral['status']==0){
            $res = db('IntegralRecord')->where($map)->setField('status',1);

            //注册消息,使用模板发送
            //$integral = db('IntegralRecord')->where($map)->find();
            $message_array['application_name'] = saas_config('global.application_name');
            $message_array['edit_time'] = date("Y-m-d H:i:s",time());
            $message_array['point'] = $integral['point'];
            Message::sendMemberMessageByTemplate($integral['member_id'], "recharge_integral", $message_array);

            if($res!==false){
                Pay::payHookReply(true);//结果输出
            }else{
                Pay::payHookReply(false);//结果输出
            }
        }else{
            Pay::payHookReply(true);//结果输出
        }
    }

    /**
     * 积分订单回调
     * @throws \pay\exception\PayException
     * @throws \think\exception\DbException
     */
    public function integral_order_hook_wx(){
        saas_init_weixin_pay();
        //参考/pay/data/Charge
        $charge=Pay::payHook($this->request->getContent());

        $order=IntegralOrder::get(substr($charge->order_id, 10));

        if(is_null($order)){
            Pay::payHookReply(true);
        }
        if($order->status==1){
            $order->status=2;
            $order->pay_type=1;
            $order->charge=$charge->getJson();
            $order->save();

            // 如果存在支付值，且使用积分大于0
            if($order->total_point > 0 && $order->total_price !== 0.00) {
                $record_data['member_id'] = $order->member_id;
                $record_data['type'] = 1;//商品兑换
                $record_data['aim_id'] = $order->order_id;//订单id
                $record_data['operate'] = 1;//支出
                $record_data['point'] = -$order->total_point;
                $record_data['status'] = 1;
                $record_data['create_time'] = time();
                $record_data['update_time'] = time();
                model('IntegralRecord')->insert($record_data);
            }

            //订单支付,使用模板发送
            $message_array['order_sn'] = $order->order_sn;
            $message_array['total_price'] = $order->total_price;
            Message::sendMemberMessageByTemplate($order->member_id, "integral_order_paid", $message_array);
        }
        Pay::payHookReply(true);
    }


    //钱包回调
    public function purseHook(){
        saas_init_weixin_pay();
        //参考/pay/data/Charge
        $charge=Pay::payHook($this->request->getContent());

        //业务处理
        $charge_array = $charge->getArray();
        $map['id'] = substr_replace($charge_array['order_id'],'',0,3);

        $integral = db('PurseRecord')->where($map)->find();
        if($integral['status']==0) {
            $res = db('PurseRecord')->where($map)->setField('status', 1);

            //charge
            $charge['type'] = $charge_array['type'];
            $charge['channel'] = $charge_array['channel'];
            $charge['order_id'] = substr_replace($charge_array['order_id'], '', 0, 3);
            $charge['order_no'] = $charge_array['order_id'];
            $charge['amount'] = $charge_array['price'];
            $charge['time_paid'] = $charge_array['pay_time'];
            $charge['transaction_no'] = $charge_array['transaction_no'];
            Db::name('MerchantCharge')->insert($charge);


            //注册消息,使用模板发送
            $price = db('PurseRecord')->where($map)->find();
            $message_array['application_name'] = saas_config('global.application_name');
            $message_array['edit_time'] = date("Y-m-d H:i:s", time());
            $message_array['price'] = $price['price'];
            Message::sendMemberMessageByTemplate($price['member_id'], "recharge_purse", $message_array);


            if ($res !== false) {
                Pay::payHookReply(true);//结果输出
            } else {
                Pay::payHookReply(false);//结果输出
            }
        }else{
            Pay::payHookReply(true);
        }

    }


    //商家收款回调
    public function receiveOrderHook(){
        saas_init_weixin_pay();
        //参考/pay/data/Charge
        $charge=Pay::payHook($this->request->getContent());

        //业务处理
        $charge_array = $charge->getArray();
        $map['order_id'] = substr_replace($charge_array['order_id'],'',0,3);

        $merchant_order = db('MerchantReceiveOrder')->where($map)->find();
        if($merchant_order['status']==0){
            $save_data['status'] = 1;
            $save_data['charge'] = json_encode($charge_array);
            $res = db('MerchantReceiveOrder')->where($map)->update($save_data);
            $receive_order = db('MerchantReceiveOrder')->where($map)->find();

            //添加钱包
            $purse_data['member_id'] = $receive_order['merchant_member'];
            $purse_data['type'] = 7;//商户微信收款
            $purse_data['aim_id'] = $receive_order['order_id'];
            $purse_data['operate'] = 1;//收入
            $purse_data['price'] = $receive_order['amount'];
            $purse_data['status'] = 1;//有效
            $purse_data['create_time'] = time();
            $purse_data['update_time'] = time();
            $res = db('PurseRecord')->insert($purse_data);


            //消息,使用模板发送
            $r_o = MerchantReceiveOrder::getDetail($receive_order['order_id']);

            if($r_o['detail']['member_id']!=0){
                $message_array['send_name'] = $r_o['detail']['nick_name'];
            }else{
                $message_array['send_name'] = null;
            }
            $message_array['application_name'] = saas_config('global.application_name');
            $message_array['edit_time'] = date("Y-m-d H:i:s",time());
            $message_array['amount'] = $charge_array['price'];
            $message_array['pay_type'] = '微信';
            Message::sendMemberMessageByTemplate($r_o['detail']['merchant_member'], "merchant_receive_purse", $message_array);

            if($r_o['detail']['member_id']!=0){
                $me_array['application_name'] = saas_config('global.application_name');
                $me_array['edit_time'] = date("Y-m-d H:i:s",time());
                $me_array['amount'] = $charge_array['price'];
                $me_array['pay_type'] = '微信';
                $me_array['merchant'] = $r_o['store_name'];
                Message::sendMemberMessageByTemplate($r_o['detail']['member_id'], "member_receive_purse", $me_array);

            }

            if($res!==false){
                Pay::payHookReply(true);//结果输出
            }else{
                Pay::payHookReply(false);//结果输出
            }
        }else{
            Pay::payHookReply(true);
        }


    }

    //商家收款支付宝支付回调
    public function aliReceiveOrderHook(){
        $config=array(
            "type"=>"ali",
            "app_id"=>saas_config('ali_pay.app_id'),
            "client_private_key"=>saas_config('ali_pay.client_private_key'),
            "ali_public_key"=>saas_config('ali_pay.ali_public_key'),
        );

        Pay::init($config);

        //参考/pay/data/Charge
        $charge=Pay::payHook($this->request->post());


        //业务处理
        $charge_array = $charge->getArray();

        $map['order_id'] = substr_replace($charge_array['order_id'],'',0,3);

        $merchant_order = db('MerchantReceiveOrder')->where($map)->find();
        if($merchant_order['status']==0){
            $save_data['status'] = 1;
            $save_data['charge'] = json_encode($charge_array);
            $res = db('MerchantReceiveOrder')->where($map)->update($save_data);
            $receive_order = db('MerchantReceiveOrder')->where($map)->find();

            //添加钱包
            $purse_data['member_id'] = $receive_order['merchant_member'];
            $purse_data['type'] = 8;//商户支付宝收款
            $purse_data['aim_id'] = $receive_order['order_id'];
            $purse_data['operate'] = 1;//收入
            $purse_data['price'] = $receive_order['amount'];
            $purse_data['status'] = 1;//有效
            $purse_data['create_time'] = time();
            $purse_data['update_time'] = time();
            $res = db('PurseRecord')->insert($purse_data);

            /* //charge
             $charge['type']=$charge_array['type'];
             $charge['channel']=$charge_array['channel'];
             $charge['order_id']=substr_replace($charge_array['order_id'],'',0,3);
             $charge['order_no']=$charge_array['order_id'];
             $charge['amount']=$charge_array['price'];
             $charge['time_paid']=$charge_array['pay_time'];
             $charge['transaction_no']=$charge_array['transaction_no'];
             db('MerchantCharge')->insert($charge);*/


            //消息,使用模板发送
            $r_o = MerchantReceiveOrder::getDetail($receive_order['order_id']);
            if($r_o['detail']['member_id']!=0){
                $message_array['send_name'] = $r_o['detail']['nick_name'];
            }else{
                $message_array['send_name'] = null;
            }
            $message_array['application_name'] = saas_config('global.application_name');
            $message_array['edit_time'] = date("Y-m-d H:i:s",time());
            $message_array['amount'] = $charge_array['price'];
            $message_array['pay_type'] = '支付宝';
            Message::sendMemberMessageByTemplate($r_o['detail']['merchant_member'], "merchant_receive_purse", $message_array);

            if($r_o['detail']['member_id']!=0){
                $me_array['application_name'] = saas_config('global.application_name');
                $me_array['edit_time'] = date("Y-m-d H:i:s",time());
                $me_array['amount'] = $charge_array['price'];
                $me_array['pay_type'] = '支付宝';
                $me_array['merchant'] = $r_o['store_name'];
                Message::sendMemberMessageByTemplate($r_o['detail']['member_id'], "member_receive_purse", $me_array);

            }


            if($res!==false){
                Pay::payHookReply(true);//结果输出
            }else{
                Pay::payHookReply(false);//结果输出
            }
        }else{
            Pay::payHookReply(true);
        }
    }

    
    public function registration_hook_wx(){
        saas_init_weixin_pay();
    	//参考/pay/data/Charge
    	$charge=Pay::payHook($this->request->getContent());
    	
    	$reg_oper=RegistrationOperate::get($charge->order_id);
    	if(is_null($reg_oper)){
    		Pay::payHookReply(true);
    	}
    	if($reg_oper->status==0){
    		$reg_oper->status=1;
    		$reg_oper->charge=$charge->getJson();
    		$reg_oper->save();
    	}
    	Pay::payHookReply(true);
    }
    
    public function agency_hook_wx(){
    	saas_init_weixin_pay();
    	//参考/pay/data/Charge
    	$charge=Pay::payHook($this->request->getContent());
    	 
    	$agency=Agency::get(substr($charge->order_id, 10));
    	if(is_null($agency)){
    		Pay::payHookReply(true);
    	}
    	$agency->agencyHandleHook($charge);
    	Pay::payHookReply(true);
    }
    
    public function order_hook_wx(){
        saas_init_weixin_pay();
    	$result = Pay::payWxOfficialHook($this->request->getContent());
        $order_data = new OrderData();
        $result['pay_type'] = 1;
        $result['result'] = $result;
        $order_data->orderPayProcess($result);
    	Pay::payHookReply(true);
    }
    
    
    public function rent_order_hook_wx(){
        saas_init_weixin_pay();
    	//参考/pay/data/Charge
    	$charge=Pay::payHook($this->request->getContent());
    
    	$order=RentOrder::get(substr($charge->order_id, 4));
    	if(is_null($order)){
    		Pay::payHookReply(true);
    	}
    	if($order->status==0){
    		$order->status=1;
    		$order->pay_type=1;
    		$order->charge=$charge->getJson();
    		$order->save();
    		//订单支付,使用模板发送
    		$message_array['order_sn'] = $order->order_sn;
    		Message::sendMemberMessageByTemplate($order->member_id, "order_paid", $message_array);
    	}
    	Pay::payHookReply(true);
    }


    //主题报名支付
    public function apply_hook_wx(){
        saas_init_weixin_pay();
        //参考/pay/data/Charge
        $charge=Pay::payHook($this->request->getContent());

        $reg_oper=SubjectApply::get($charge->order_id);

        if(is_null($reg_oper)){
            Pay::payHookReply(true);
        }
        if($reg_oper->status==0){
            $reg_oper->status=1;
            $reg_oper->pay_type=1;
            $reg_oper->charge=$charge->getJson();
            //邀请码
            $reg_oper->invite_code = uniqid();
            $reg_oper->save();
            //发送手机号
            $form_map['apply_id'] = $charge->order_id;
            $form = SubjectApplyForm::get($form_map);
            $one = json_decode($form['person_form'],true);
            foreach($one as $k=>$v){
                $mobile = null;
                //发送短信告知邀请码
                foreach($v as $kv=>$vv){
                    if($vv['name']=="手机"){
                        $mobile = $vv['value'];
                    }
                }
                if(!empty($mobile)){
                    \sms\SmsUtil::getApplyInviteCode($mobile, $reg_oper->invite_code);
                }

            }

        }
        Pay::payHookReply(true);
    }

    /**
     * VIP套餐购买异步回调
     * @throws \pay\exception\PayException
     * @throws \think\exception\DbException
     */
    public function vip_hook_wx(){
        saas_init_weixin_pay();
        //参考/pay/data/Charge
        $charge=Pay::payHook($this->request->getContent());

        $package_order = VipPackageOrder::get(substr($charge->order_id, 10));
        if(is_null($package_order)){
            Pay::payHookReply(true);
        }
        // 订单处理
        $package_order->vipHandleHook($charge);
        // VIP信息处理
        $package_order->vipHandleVip();
        Pay::payHookReply(true);
    }

    public function groupbuy_wx_official_pay()
    {
        saas_init_weixin_pay();
        $result = Pay::payWxOfficialHook($this->request->getContent());
        $result['pay_type'] = 1;
        $result['result'] = $result;
        GroupbuyOrder::orderPayProcess($result);
        Pay::payHookReply(true);
    }

    /**
     * 礼包支付异步回调
     * @throws \pay\exception\PayException
     */
    public function package_order_hook_wx(){
        saas_init_weixin_pay();
        $result = Pay::payWxOfficialHook($this->request->getContent());
        $order_data = new PackageOrder();
        $result['pay_type'] = 1;
        $result['result'] = $result;
        $order_data->orderPayProcess($result);
        Pay::payHookReply(true);
    }
}

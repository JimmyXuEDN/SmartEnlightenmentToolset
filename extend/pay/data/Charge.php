<?php
namespace pay\data;

/**
 * 统一支付回调实体
 * @author Zhong
 *
 */
class Charge{
	
	public $type;//支付平台:wx,ali,union
	public $channel;//支付类型:app,web,h5
	public $order_id;//系统订单号
	public $attach;
	public $price;
	public $pay_time;
	public $transaction_no;//平台订单号
	
	
	public function  getArray(){
		$res['type']=$this->type;
		$res['channel']=$this->channel;
		$res['order_id']=$this->order_id;
		$res['attach']=$this->attach;
		$res['price']=$this->price;
		$res['pay_time']=$this->pay_time;
		$res['transaction_no']=$this->transaction_no;
		
		return $res;
	}
	
	public function getJson(){
		return json_encode($this->getArray());
	}
	
}
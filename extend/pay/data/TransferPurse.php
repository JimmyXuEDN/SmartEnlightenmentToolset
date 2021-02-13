<?php
namespace pay\data;

/**
 * 付款到个人(零钱)
 * @author Zhong
 *
 */
class TransferPurse{
	/**---------必须---------**/
	public $order_id;//系统惟一单号
	public $price;//支付价格,单位:分
    public $real_name;//收款用户真实姓名
	public $openid;//openid
    public $remark; //备注
}
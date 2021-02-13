<?php
namespace pay\data;

/**
 * 统一支付下单实体
 * @author Zhong
 *
 */
class Order{
	/**---------必须---------**/
	public $order_id;//系统惟一单号
	public $title;//支付标题
	public $price;//支付价格,单位:元
	
	/**---------不必须---------**/
	public $attach;//附加参数,字符串
	public $detail;//支付详情
}
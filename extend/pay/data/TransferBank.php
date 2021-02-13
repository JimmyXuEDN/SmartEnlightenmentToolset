<?php
namespace pay\data;

/**
 * 付款到个人(银行卡)
 * @author Zhong
 *
 */
class TransferBank{
	/**---------必须---------**/
	public $order_id;//系统惟一单号
	public $price;//支付价格,单位:分
    public $real_name;//收款用户真实姓名
	public $bank_no;//银行卡号
	public $bank_code;//银行开户行,查看微信:银行编号列表:https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=24_4
    public $remark; //备注
}
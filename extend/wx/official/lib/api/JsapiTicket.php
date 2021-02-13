<?php
namespace wx\official\lib\api;

use wx\official\lib\Util;

/**
 * 微信公众号:jsapi ticket
 * @author Zhong
 *
 */
class JsapiTicket extends Base{
	
	public function getTicket(){
		$url="ticket/getticket?access_token=".Util::getAccessToken()."&type=jsapi";
		
		$res=$this->get($url);
		
		return $res;
	}
}
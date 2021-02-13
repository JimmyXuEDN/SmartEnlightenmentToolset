<?php
namespace wx\official\lib\api;

use wx\official\lib\Config;

/**
 * 微信公众号:access token
 * @author Zhong
 *
 */
class AccessToken extends Base{
	
	public function getToken(){
		$url="token?grant_type=client_credential&appid=".Config::$app_id."&secret=".Config::$app_secret;
		
		$res=$this->get($url);
		
		return $res;
	}
}
<?php
namespace wx\official\lib\api;

use wx\official\lib\Util;

/**
 * 微信公众号:用户管理 
 * @author Zhong
 *
 */
class User extends Base{
	
	public function getDetail($openid){
		$url="user/info?access_token=".Util::getAccessToken()."&openid=$openid&lang=zh_CN";
		return $this->get($url);
	}
}
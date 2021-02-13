<?php
namespace wx\official\lib;

use wx\official\lib\api\AccessToken;
use wx\official\lib\api\JsapiTicket;
use wx\official\lib\api\TemplateMessage;

class Util{
	/**
	 * 验证签名
	 * @param unknown $signture
	 * @param unknown $timestamp
	 * @param unknown $nonce
	 * @param unknown $token
	 */
	public static function auth($signture,$timestamp,$nonce,$token){
		$tmpArr = array($timestamp, $nonce,$token);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if( $signture==$tmpStr ){
			return true;
		}else{
			return false;
		}
	}
	
	public static function getAccessToken(){
		$access_token=cache('wx_official_access_token');
		if(!$access_token){
			$accessToken=new AccessToken();
			$res=$accessToken->getToken();
			cache('wx_official_access_token',$res['access_token'],$res['expires_in']-5*60);
			$access_token=$res['access_token'];
		}
		return $access_token;
		
	}
	
	public static function getJsapiTicket(){
		$jsapi_ticket=cache('wx_official_jsapi_ticket');
		if(!$jsapi_ticket){
			$jsapiTicket=new JsapiTicket();
			$res=$jsapiTicket->getTicket();
			cache('wx_official_jsapi_ticket',$res['ticket'],$res['expires_in']-2*60);
			$jsapi_ticket=$res['ticket'];
		}
		return $jsapi_ticket;
	
	}
	
	
	public static function getJsapiSignatrue($sign_url){
		$sing_array['noncestr']=self::getNonceStr();
		$sing_array['jsapi_ticket']=self::getJsapiTicket();
		$sing_array['timestamp']=time();
		$sing_array['url']=$sign_url;
		//var_dump($sing_array);
		
		$sing_ture=self::MakeSign($sing_array);
		//var_dump($sing_ture);
		$result['appId']=Config::$app_id;
		$result['timestamp']=$sing_array['timestamp'];
		$result['nonceStr']=$sing_array['noncestr'];
		$result['jsapi_ticket']=$sing_array['jsapi_ticket'];
		$result['signature']=$sing_ture;
		
		return $result;
	}
	
	/**
	 * 发送模板消息
	 * @param string $openid 用户openid
	 * @param string $template_id 模板id
	 * @param array $data 模板参数
	 * @param string $jump_url 跳转url
	 * @param array $miniprogram 跳转小程序
	 * @return string 消息id
	 */
	public static function sendTemplateMessage($openid,$template_id,$data,$jump_url=null,$miniprogram=null){
	    $temp=new TemplateMessage();
	    $res=$temp->sendTemplateMessage($openid, $template_id, $data,$jump_url,$miniprogram);
	    return $res['msgid'];
	}
	
	
	/**
	 *
	 * 产生随机字符串，不长于32位
	 * @param int $length
	 * @return 产生的随机字符串
	 */
	public static function getNonceStr($length = 32)
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
		}
		return $str;
	}
	
	/**
	 * 格式化参数格式化成url参数
	 */
	public static  function ToUrlParams($arr)
	{
		$buff = "";
		foreach ($arr as $k => $v)
		{
			if($k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
	
		$buff = trim($buff, "&");
		return $buff;
	}
	
	/**
	 * 生成签名
	 * @return 签名
	 */
	public static  function MakeSign($arr)
	{
		//签名步骤一：按字典序排序参数
		ksort($arr);
		$string = self::ToUrlParams($arr);
		//签名步骤三：MD5加密
		//var_dump($string);
		$string = sha1($string);
		return $string;
	}
}
<?php
namespace wx\official\lib;

class Web {
	
	private $appid="";
	
	private $secret="";
	
	public function __construct(){
		$this->appid=saas_config('wx_official.web_app_id');
		$this->secret=saas_config('wx_official.web_app_secret');
	}
	public  function get_user_info($scope,$code){
		$access_res=cache('wx_access_'.$code);
		if(!$access_res){
			//echo $this->appid.'-'.$this->secret;
			$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appid."&secret=".$this->secret."&code=".$code."&grant_type=authorization_code";
			
			$access_res=json_decode($this->push_curl($url),true);
			//var_dump($access_res);
			if(key_exists('errcode', $access_res)){
				$result['status']=2000;
				$result['data']=$access_res;
					
				return $result;
			}
			
			cache('wx_access_'.$code,$access_res);
			cache('wx_access_'.$code.'time',time());
		}
		if((time()-cache('wx_access_'.$code.'time'))>1.5*60*60){
			//刷新
			$url="https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=".$this->appid."&grant_type=refresh_token&refresh_token=".$access_res['refresh_token'];
			$access_res=json_decode($this->push_curl($url),true);
			if(key_exists('errcode', $access_res)){
				$result['status']=2000;
				$result['data']=$access_res;
			
				return $result;
			}
				
			cache('wx_access_'.$code,$access_res);
			cache('wx_access_'.$code.'time',time());
		}
		if($scope=='snsapi_base'){
			$result['status']=0;
			$result['data']=$access_res;
			return $result;
		}
		//echo $access_res['access_token'];
		$url="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_res['access_token']."&openid=".$access_res['openid'];
		$res=json_decode($this->push_curl($url),true);
		//var_dump($res);
		if(key_exists('errcode', $res)){
			$result['status']=2000;
			$result['data']=$res;
			return $result;
			
		}
		$result['status']=0;
		$result['data']=$res;
		return $result;
	}
	
	public function get_jsapi_signatrue($sign_url){
		$jsapi_ticket=cache('wx_jsapi_ticket');
		if(!$jsapi_ticket){
			$access_token=cache('wx_access_token');
			if(!$access_token){
				//echo "no cahe";
				$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->secret;
				
				$access_res=json_decode($this->push_curl($url),true);
				//var_dump($access_res);
				if($access_res['errcode']){
					$result['status']=2000;
					$result['data']=$access_res;
						
					return $result;
				}
				
				cache('wx_access_token',$access_res);
				cache('wx_access_token_time',time());
			}
			if((time()-cache('wx_access_token_time'))>1.5*60*60){
				//刷新
				$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->secret;
				
				$access_res=json_decode($this->push_curl($url),true);
				//var_dump($access_res);
				if($access_res['errcode']){
					$result['status']=2000;
					$result['data']=$access_res;
						
					return $result;
				}
				
				cache('wx_access_token',$access_res);
				cache('wx_access_token_time',time());
			}
			$url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_res['access_token']."&type=jsapi";
			$res=json_decode($this->push_curl($url),true);
			//var_dump($res);
			if($res['errcode']){
				$result['status']=2000;
				$result['data']=$res;
				return $result;
			
			}
			$jsapi_ticket=$res['ticket'];
			cache('wx_jsapi_ticket',$jsapi_ticket,7000);
		}
	
		$sing_array['noncestr']=$this->getNonceStr();
		$sing_array['jsapi_ticket']=$jsapi_ticket;
		$sing_array['timestamp']=time();
		$sing_array['url']=$sign_url;
		//var_dump($sing_array);
		$sing_ture=$this->MakeSign($sing_array);
		//var_dump($sing_ture);
		$result['status']=0;
		$result['data']['appId']=$this->appid;
		$result['data']['timestamp']=$sing_array['timestamp'];
		$result['data']['nonceStr']=$sing_array['noncestr'];
		$result['data']['signature']=$sing_ture;
		return $result;
	}
	
	private function  push_curl($url){
		$ch = curl_init() ;
		curl_setopt($ch,CURLOPT_URL, $url);
// 		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
// 		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);//可以本地提交
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);//可以本地提交
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		 $output = curl_exec($ch) ;
		 
		 return $output;
	}
	
	/**
	 *
	 * 产生随机字符串，不长于32位
	 * @param int $length
	 * @return 产生的随机字符串
	 */
	private function getNonceStr($length = 32)
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
	private  function ToUrlParams($arr)
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
	 * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
	 */
	private  function MakeSign($arr)
	{
		//签名步骤一：按字典序排序参数
		ksort($arr);
		$string = $this->ToUrlParams($arr);
		//签名步骤三：MD5加密
		//var_dump($string);
		$string = sha1($string);
		return $string;
	}
   
}
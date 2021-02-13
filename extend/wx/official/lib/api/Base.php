<?php
namespace wx\official\lib\api;
use think\facade\Log;
use wx\official\lib\Config;

/**
 * 调用微信公众号的接口
 * @author Zhong
 *
 */
class Base{
	protected $values = array();
	protected $host = "https://api.weixin.qq.com/cgi-bin/";

	public function __construct()
	{
	    \wx\official\lib\Config::setConfig(saas_config('wx_official'));
	}
	/**
	 * 获取设置的值
	 */
	public function GetValues()
	{
		return $this->values;
	}

	public function get($url){
		return $this->curl($url, 'get');

	}

	/**
	 * json提交方式
	 * @param unknown $url
	 * @return mixed
	 */
	public function post($url){
		return $this->curl($url, 'post',json_encode($this->values,JSON_UNESCAPED_UNICODE));

	}

	/**
	 * 表单提交方式
	 * @param unknown $url
	 * @return mixed
	 */
	public function post_form($url){
	    return $this->curl($url, 'post',$this->values);
	}

	public function curl($url,$method,$data=null){
		$url=$this->host.$url;
		$this->log("wx official api $method :$url");
		$this->log("wx official api data :".var_export($this->values,true));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);//可以本地提交
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);//可以本地提交
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		if($data){
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		$output=curl_exec($ch);
		$err = curl_error($ch);

		curl_close($ch);

		if ($err) {
			saas_abort(ERROR_SYSTEM, [], "curl错误！$err");
		} else {

		}
		$this->log("wx official api result :".$output);
		$res=json_decode($output,true);
		if(isset($res['errcode'])&&$res['errcode']!=0){
            saas_abort(ERROR_SYSTEM, [], $res['errcode']."--".$res['errmsg']);
		}
		return $res;
	}

	public function log($message){
		if(Config::$is_debug)
			Log::write($message,'official');
	}
}

<?php
namespace wx\official\lib;
/**
 * 配置类.
 * @author Zhong
 *
 */
class Config{
	
	public static $is_debug=true;
	public static $app_id='';
	public static $app_secret='';
	public static $app_encoding_key='';
	public static $is_encrypt=0;

	
	public static function setConfig($config){
		self::$app_id=$config['app_id'];
		self::$app_secret=$config['app_secret'];
		self::$app_encoding_key=$config['encoding_aes_key'];
		self::$is_encrypt=$config['is_encrypt'];
	}
}
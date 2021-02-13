<?php

namespace pay;

use think\facade\Log;

/**
 * 支付的工具类
 * @author Zhong
 *
 */
class Util
{

    public static function log($message)
    {
        Log::write($message, 'pay');
    }

    public static function wx_rsa_encrypt($data)
    {
        $key_path = root_path('extend') . DIRECTORY_SEPARATOR . "pay" . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "wx" . DIRECTORY_SEPARATOR . "cert" . DIRECTORY_SEPARATOR . "pay_to_bank_public_key.pem";
        $pubkey = file_get_contents($key_path); //公钥
        openssl_public_encrypt($data, $encrypted, $pubkey, OPENSSL_PKCS1_OAEP_PADDING);//公钥加密
        $encrypted = base64_encode($encrypted);// base64传输
        return $encrypted;
    }
}
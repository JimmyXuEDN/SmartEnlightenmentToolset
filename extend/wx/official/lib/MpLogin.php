<?php
/**
 * Created by KalamXu.
 * User: KalamXu
 * Date: 2018/7/20
 * Time: 11:13
 */

namespace wx\official\lib;


class MpLogin
{
    private $appid = "";

    private $secret = "";

    public function __construct()
    {
        $this->appid = saas_config('wx_mp.app_id');
        $this->secret = saas_config('wx_mp.app_secret');
    }

    /**
     *
     * 小程序登录凭证校验
     * @param string $code
     * @param string $appid
     * @param string $secret
     * @return array|mixed
     */
    public function mp_code_verify($code, $appid = '', $secret = '')
    {
        $appid = empty($appid) ? $this->appid : $appid;
        $secret = empty($secret) ? $this->secret : $secret;
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $appid . "&secret=" . $secret . "&js_code=" . $code . "&grant_type=authorization_code";
        return json_decode($this->push_curl($url), true);
    }

    /**
     *
     * CURL请求
     * @param string $url
     * @return 请求结果
     */
    private function push_curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
// 		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
// 		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//可以本地提交
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//可以本地提交
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($ch);

        return $output;
    }
}
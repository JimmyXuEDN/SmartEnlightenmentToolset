<?php

namespace app\base\util;


use AlibabaCloud\Client\AlibabaCloud;
use Exception;

class SmsUtil
{
    /**
     * 发送短信
     * @param string $tel
     * @param array $params
     * @param string $signName
     * @param string $templateCode
     * @return bool
     */
    public static function sendSms($tel, $signName, $templateCode, $params = [])
    {
        try {
            AlibabaCloud::accessKeyClient(saas_config("ali_sms.app_key"), saas_config("ali_sms.app_secret"))
                ->regionId('cn-hangzhou')
                ->asDefaultClient();

            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
                        'PhoneNumbers' => $tel,
                        'SignName' => $signName,
                        'TemplateCode' => $templateCode,
                        'TemplateParam' => json_encode($params),
                    ],
                ])
                ->request();

        } catch (Exception $e) {
            saas_abort(ERROR_SYSTEM, null, $e->getMessage());
        }

        if ($result->Code != 'OK') {
            saas_abort(ERROR_SYSTEM, null, $result->Message);
        }
        return true;
    }

    /**
     * 发送验证短信,自动发送6位验证码,验证码缓存15分钟,要使用 checkAuthMessage方法验证短信验证码
     * @param string $tel 电话号码
     */
    public static function sendAuthMessage($tel)
    {
        $code = rand(100000, 999999);
        $params['code'] = $code . "";
        self::sendSms($tel, saas_config("ali_sms.sign_name"), saas_config("ali_sms.template_auth"), $params);
        cache('sms_auth_' . $tel, $code, 15 * 60);
    }

    /**
     * 验证短信验证码
     * @param string $tel
     * @param string $code
     * @return bool
     */
    public static function checkAuthMessage($tel, $code)
    {
        $send_code = cache('sms_auth_' . $tel);
        if ($send_code && $send_code == $code) {
            return true;
        }
        return false;
    }

    /**
     * 快递取件码
     * @param string $tel
     * @param string $code
     * @param $merchantname
     * @param $merchantaddress
     */
    public static function getCourierCode($tel, $code, $merchantname, $merchantaddress)
    {
        $params['code'] = $code . "";
        $params['merchantname'] = $merchantname;
        $params['merchantaddress'] = $merchantaddress;
        self::sendSms($tel, saas_config("ali_sms.sign_name"), saas_config("ali_sms.courier_template_auth"), $params);
    }


    /**
     * 主题报名成功发送邀请码
     * @param string $tel
     * @param string $code
     */
    public static function getApplyInviteCode($tel, $code)
    {
        $params['code'] = $code . "";
        self::sendSms($tel, saas_config("ali_sms.sign_name"), saas_config("ali_sms.apply_invite_code"), $params);
    }

    /**
     * @param $tel
     * @param $name
     * @param $address
     * @param $date
     */
    public static function getConferenceNoticeCode($tel, $name, $address, $date)
    {
        $params['title'] = $name;
        $params['address'] = $address;
        $params['date'] = $date;
        self::sendSms($tel, saas_config("ali_sms.sign_name"), saas_config("ali_sms.add_conference"), $params);
    }
}
<?php

namespace app\mp\model;

use app\base\model\BaseModel;
use Curl\Curl;

class Mp extends BaseModel
{

    protected $pk = 'mp_id';

    /**
     * @return mixed
     * @throws \ErrorException
     */
    public function getAccessToken()
    {
        $token = cache('mp_access_token');
        if (is_null($token) || empty($token)) {
            $data['grant_type'] = 'client_credential';
            $data['appid'] = $this->getAttr('app_id');
            $data['secret'] = $this->getAttr('app_secret');

            $curl = new Curl();
            $curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
            $curl->setOpt(CURLOPT_SSL_VERIFYHOST, FALSE);
            $curl->get($this->host . $this->token, $data);

            if ($curl->error_code !== 0) {
                saas_abort(2000, [], $curl->info);
            }

            $data = json_decode($curl->response, true);

            cache('mp_access_token', $data['access_token'], $data['expires_in']);
        }

        return cache('mp_access_token');
    }

    /**
     * 生成大量小程序二维码
     * $data 参考：https://developers.weixin.qq.com/miniprogram/dev/api/open-api/qr-code/getWXACodeUnlimit.html?q=
     * @param $data
     * @return string
     * @throws \ErrorException
     */
    public function qr($data)
    {
        if (empty($data) || !isset($data['scene'])) {
            return null;
        }
        $data = json_encode($data, JSON_UNESCAPED_SLASHES);

        $curl = new Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, FALSE);
        $curl->setOpt(CURLOPT_RETURNTRANSFER, 1);
        $curl->setOpt(CURLOPT_POST, 1);
        $curl->setHeader('content-type', 'application/json');
        $curl->post($this->host . $this->qr . '?access_token=' . $this->getAccessToken(), $data);

        if ($curl->error_code !== 0) {
            return null;
        }

        return $curl->response;
    }

    public function getByCode($code)
    {

    }
}
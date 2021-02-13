<?php
/**
 * 高德地图webservice api model
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/26
 * Time: 20:36
 */

namespace app\base\model;

/**
 * Class AMap
 * @package
 */
class AMap extends BaseModel
{
    private $amap_key;
    private $amap_secret;
    private $webservice_host;

    /**
     * 初始化
     */
    function initialize()
    {
        parent::initialize();
        $this->amap_key = '6219f062d9a237d8ac462efdd61893be';
        $this->amap_secret = 'aeabbd7884f13f88190832274fd36867';
        $this->webservice_host = 'https://restapi.amap.com/v3';
    }

    /**
     * 签名
     * @param $params
     * @return string
     */
    private function sign($params)
    {
        ksort($params);
        $param_string = http_build_query($params);

        return urlencode(md5($param_string + $this->amap_secret));
    }

    /**
     * 周边搜索
     * @param $location
     * @param $types
     * @param $keywords
     * @param $radius
     * @param $sortrule
     * @param $offset
     * @param $page
     * @param $city
     * @return mixed
     * @throws \ErrorException
     */
    public function place_around($location, $types, $keywords, $radius, $sortrule, $offset, $page, $city)
    {
        $data['location'] = $location;
        $data['types'] = $types;
        $data['keywords'] = $keywords;
        $data['city'] = $city;
        $data['radius'] = $radius;
        $data['sortrule'] = $sortrule;
        $data['offset'] = $offset;
        $data['page'] = $page;
        $data['key'] = $this->amap_key;
        $data['extensions'] = 'all';

        $data['sig'] = $this->sign($data);

        $curl = new \Curl\Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, FALSE);
        $curl->get($this->webservice_host . '/place/around', $data);

        if ($curl->error_code !== 0) {
            trace('response:' . $curl->response, 'error');
            saas_send_response(2000, [], $curl->info);
        }

        return $data = json_decode($curl->response, true);
    }

    /**
     * 关键词搜索
     * @param $keywords
     * @param $types
     * @param $city
     * @param $offset
     * @param $page
     * @return mixed
     * @throws \ErrorException
     */
    public function place_text($keywords, $types, $city, $offset, $page)
    {
        $data['keywords'] = $keywords;
        $data['types'] = $types;
        $data['city'] = $city;
        $data['citylimit'] = true;
        $data['offset'] = $offset;
        $data['page'] = $page;
        $data['key'] = $this->amap_key;
        $data['extensions'] = 'all';

        $data['sig'] = $this->sign($data);

        $curl = new \Curl\Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, FALSE);
        $curl->get($this->webservice_host . '/place/text', $data);

        trace('request header:' . json_encode($curl->request_headers), 'error');
        trace('response header:' . json_encode($curl->response_headers), 'error');

        if ($curl->error_code !== 0) {
            saas_send_response(2000, [], $curl->info);
        }

        return $data = json_decode($curl->response, true);
    }

    /**
     * @param $location
     * @param $poitype
     * @param $radius
     * @return mixed
     * @throws \ErrorException
     */
    public function regeo($location, $poitype, $radius)
    {
        $data['location'] = $location;
        $data['poitype'] = $poitype;
        $data['radius'] = $radius;
        $data['key'] = $this->amap_key;
        $data['extensions'] = 'all';

        $data['sig'] = $this->sign($data);

        $curl = new \Curl\Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, FALSE);
        $curl->get($this->webservice_host . '/geocode/regeo', $data);

        if ($curl->error_code !== 0) {
            saas_send_response(2000, [], $curl->info);
        }

        return $data = json_decode($curl->response, true);
    }
}
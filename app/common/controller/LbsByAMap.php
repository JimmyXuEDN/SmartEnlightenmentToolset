<?php
/**
 * 高德地图webservice api
 * Created by PhpStorm.
 * User: mzy
 * Date: 2020/2/25
 * Time: 17:34
 */

namespace app\common\controller;

use app\BaseController;
use app\base\model\AMap;
use app\base\model\GlobalAmapCity;
use app\base\model\GlobalAmapPoi;

/**
 * Class LbsByAmap
 * @package general
 */
class LbsByAMap extends BaseController
{
    /**
     * 城市编码
     */
    public function city()
    {
        $res = GlobalAmapCity::get_list();
        $this->sendResponse(0, $res);
    }

    /**
     * poi
     */
    public function poi()
    {
        $res = GlobalAmapPoi::get_list();
        $this->sendResponse(0, $res);
    }

    /**
     * @throws \ErrorException
     */
    public function place_around()
    {
        // 坐标
        $location = $this->getParams('location', true);

        // POI信息
        $types = $this->getParams('types', false);

        // 关键词
        $keywords = $this->getParams('keywords', false);

        // 城市
        $city = $this->getParams('city', false);
        $city = isset($city) ? $city : '440300';

        // 搜索半径
        $radius = $this->getParams('radius', false);
        $radius = isset($radius) ? $radius : 3000;

        // 排序方式
        $sortrule = $this->getParams('sortrule', false);
        $sortrule = isset($sortrule) ? $sortrule : 'distance';

        $page_size = $this->getParams('page_size', false);
        $page_size = isset($page_size) ? $page_size : 10;
        $page = $this->getParams('page', false);
        $page = isset($page) ? $page : 1;
        $amap = new AMap();
        $res = $amap->place_around($location, $types, $keywords, $radius, $sortrule, $page_size, $page, $city);
        if ($res['status'] != 1) {
            $this->sendResponse(2000, $res, $res['info']);
        }

        $this->sendResponse(0, $res);
    }

    /**
     * @throws \ErrorException
     */
    public function place_text()
    {
        $keywords = $this->getParams('keywords', true);

        // POI信息
        $types = $this->getParams('types', false);

        // 城市
        $city = $this->getParams('city', false);
        $city = isset($city) ? $city : '440300';

        $page_size = $this->getParams('page_size', false);
        $page_size = isset($page_size) ? $page_size : 10;
        $page = $this->getParams('page', false);
        $page = isset($page) ? $page : 1;
        $amap = new AMap();
        $res = $amap->place_text($keywords, $types, $city, $page_size, $page);
        if ($res['status'] != 1) {
            $this->sendResponse(2000, $res, $res['info']);
        }

        $this->sendResponse(0, $res);
    }
}
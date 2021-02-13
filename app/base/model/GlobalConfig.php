<?php
/**
 * Created by PhpStorm.
 * User: mzy
 * Date: 2020/3/21
 * Time: 17:13
 */

namespace app\base\model;

/**
 * Class GlobalConfig
 * @package general
 */
class GlobalConfig extends BaseModel
{
    /**
     * 修改choose_value
     * @param $value
     * @return string
     */
    public function setChooseValueAttr($value)
    {
        return json_encode($value);
    }

    /**
     * 修改choose_value
     * @param $value
     * @return mixed
     */
    public function getChooseValueAttr($value)
    {
        return json_decode($value);
    }

    /**
     * @return \think\model\relation\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo('GlobalConfigType');
    }
}
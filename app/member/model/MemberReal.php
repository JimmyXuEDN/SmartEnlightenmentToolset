<?php

namespace app\member\model;

use app\base\model\BaseModel;

class MemberReal extends BaseModel
{

    /**
     * 自动转换photos
     * @param $value
     * @return string
     */
    public function setPhotosAttr($value)
    {
        return json_encode($value);
    }

    /**
     * 自动转换photos
     * @param $value
     * @return mixed
     */
    public function getPhotosAttr($value)
    {
        return json_decode($value);
    }

    /**
     * 属于一个member
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }
}
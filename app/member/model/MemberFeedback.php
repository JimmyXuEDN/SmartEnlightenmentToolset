<?php

namespace app\member\model;

use app\base\model\BaseModel;

class MemberFeedback extends BaseModel
{

    /**
     * @param $value
     * @return string
     */
    public function setPhotosAttr($value){
        $photos = json_encode($value);
        return $photos;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getPhotosAttr($value)
    {
        $photos = json_decode($value,true);
        return $photos;
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

}
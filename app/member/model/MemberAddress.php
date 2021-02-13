<?php

namespace app\member\model;

use app\base\model\BaseModel;

class MemberAddress extends BaseModel
{
    protected $pk = "addr_id";

    //地址列表
    public static function getAddressList($member_id)
    {
        $map['member_id'] = $member_id;
        $map['status'] = 1;
        return self::getList([], [], $map);
    }

}
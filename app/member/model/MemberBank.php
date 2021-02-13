<?php

namespace app\member\model;

use app\base\model\BaseModel;
use app\merchant\model\MerchantBank;

class MemberBank extends BaseModel
{
    public function bank()
    {
        return $this->belongsTo(MerchantBank::class, 'bank_id', 'bank_id');
    }

    //银行卡列表
    public static function getBankList($member_id)
    {
        $map['member_id'] = $member_id;
        $with = ["bank"];
        return self::getList($with, [], $map);
    }

    //银行卡详情
    public static function getBankDetail($bank_id){
        $with = ["bank"];
        $res['bank'] = self::with($with)->find($bank_id);
        return $res;
    }
}
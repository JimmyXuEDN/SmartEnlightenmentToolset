<?php

namespace app\member\model;

use app\base\model\BaseModel;
use app\goods\model\GoodsItem;

class MemberCollection extends BaseModel
{

    protected $pk = "collection_id";

    public function goodsItem()
    {
        $bind_array = array(
            "name",
            "cover",
            "pre_price",
            "price"
        );
        return $this->belongsTo(GoodsItem::class, 'aim_id', 'item_id')->bind($bind_array);
    }

    //收藏列表
    public static function getCollectionList($member_id, $type = 1)
    {
        $map['member_id'] = $member_id;
        $with = ['goodsItem'];
        return self::getList($with, array(), $map);
    }

    // 删除收藏
    public static function deleteCollect($id)
    {
        return self::destroy($id);
    }
}
<?php


namespace app\member\model;


use app\base\model\BaseModel;
use app\goods\model\GoodsAttributeValue;
use app\goods\model\GoodsItem;
use app\goods\model\GoodsItemAttr;
use app\goods\model\GoodsItemSku;

class MemberCart extends BaseModel
{
    //购物车列表
    public static function getCartList($member_id)
    {
        $map['member_id'] = $member_id;
        $with = ['sku'];
        $res = self::getList($with, [], $map);
        foreach ($res['list'] as $k => $v) {
            $item = GoodsItem::find($v['item_id']);
            $res['list'][$k]['name'] = $item['name'];
            $sku = GoodsItemSku::find($v['sku_id']);
            $attr_array = array();
            $sku_array = array();
            $attibues = GoodsItemAttr::with('goodsAttribute')->where('item_id', $v['item_id'])->select()->toArray();
            foreach ($attibues as $ak => $av) {
                if ($av['attr_type'] == 3) {
                    $sku_value_array = GoodsAttributeValue::where('attr_val_id', 'in', $av['attr_val_ids'])->field('attr_val_id,val')->select();
                    $av['attribute_value'] = $sku_value_array;
                    unset($av['id']);
                    unset($av['attr_val']);
                    unset($av['create_time']);
                    unset($av['attr_type']);
                    unset($av['input_type']);
                    unset($av['update_time']);
                    $sku_array[] = $av;
                    foreach ($sku_value_array as $value) {
                        if (in_array($value['attr_val_id'], $sku->properties_ids)) {
                            $av['attr_val'] = $value['val'];
                            unset($av['attribute_value']);
                            $attr_array[] = $av;
                        }
                    }

                } else {
                    //spu
                    if ($av['input_type'] != 1) {
                        $value_array = GoodsAttributeValue::where('attr_val_id', 'in', $av['attr_val_ids'])->column('val');
                        $av['attr_val'] = implode(',', $value_array);
                    }
                    unset($av['id']);
                    unset($av['create_time']);
                    unset($av['input_type']);
                    unset($av['update_time']);
                    $attr_array[] = $av;
                }
            }
            $res['list'][$k]['attribute'] = $attr_array;
            $res['list'][$k]['sku_attribute'] = $sku_array;

            $sku_list = GoodsItemSku::where('item_id', $v['item_id'])->select();
            foreach ($sku_list as $skk => $skv) {
                $attr_val_array = array();
                if (is_array($skv['properties_ids'])) {
                    $v_array = GoodsAttributeValue::where('attr_val_id', 'in', $skv['properties_ids'])->field('attr_val_id,val')->select();
                }
                $skv['attribute_value'] = $v_array;
                $res['list'][$k]['sku'][] = $skv;
            }

        }
        return $res;
    }

    //Sku信息
    public function sku()
    {

        $bind_array = array(
            "attr_name" => "properties_name",
            "cover",
            'price',
            'inventory',
            'item_id'
        );//一对一关联时,绑定属性相当于join查询,需要返回列表时必须绑定.

        return $this->belongsTo(GoodsItemSku::class, 'sku_id', 'sku_id')->bind($bind_array);
    }

}
<?php

namespace app\base\model;

use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

class BaseModel extends Model
{
    protected static $allowSearch = [];//允许搜索的字段
    protected static $allowSort = [];//允许排序的字段

    /**
     *
     * @param array $with 预载入查询,数组,就是绑定关联模型的方法名.驼峰式,如:memberToken
     * @param array $field 返回的字段,可以直接使用一对一模型绑定过的字段,如果要限制一对多关联的返回字段,需要添加前缀.前缀为小写加下划线,如:member_address.mobile
     * @param array $map 查询条件,一般不需要填写.由地址栏参数传入
     * @param array $order 排序条件,一般不需要填写.由地址栏参数传入
     * @param array $withCount 统计字段，一对多或者多对多关联传入
     * @param string $has
     * @param array $hasMap
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function getList($with = [], $field = [], $map = [], $order = [], $withCount = [], $has = '', $hasMap = [])
    {
        //处理排序
        if (request()->has('sb')) {
            $sb_array = explode('-', request()->param('sb'));
            $lo_array = explode('-', request()->param('lo'));
            foreach ($sb_array as $key => $value) {
                if (!static::$allowSort || in_array($value, static::$allowSort)) {
                    $order[$value] = $lo_array[$key];
                }
            }
        }

        //搜索条件
        if (request()->has('kb')) {
            $kb_array = explode('-', trim(request()->param('kb')));
            $kt_array = explode('-', trim(request()->param('kt')));
            $kv_array = explode('-', trim(request()->param('kv')));
            self::condition2map($map, $kb_array, $kt_array, $kv_array);
        }

        // 子对象查询条件
        if (request()->has('has')) {
            $has = trim(request()->param('has'));

            $kb_array = explode('-', trim(request()->param('hkb')));
            $kt_array = explode('-', trim(request()->param('hkt')));
            $kv_array = explode('-', trim(request()->param('hkv')));
            self::condition2map($hasMap, $kb_array, $kt_array, $kv_array);
        }

        $result = [];
        $model = new static();
        if ($has) {
            $model = $model->hasWhere($has, $hasMap);
        }

        if ($with) {
            $model = $model->with($with);
        }

        if ($withCount) {
            $model = $model->withCount($withCount);
        }

        if ($map) {
            $model = $model->where($map);
        }

        if ($order) {
            $model = $model->order($order);
        }

        if (request()->has('p')) {
            $list = $model->paginate([
                'list_rows' => request()->param('ps', 10),
                'var_page' => 'p',
            ]);
            $result['list'] = $list->getCollection()->visible($field)->toArray();
            $result['responsePage'] = saas_response_page($list->currentPage(), $list->listRows(), $list->total());
            return $result;
        }
        $result['list'] = $model->select()->visible($field)->toArray();
        return $result;
    }

    /**
     * 查询条件转换
     * @param array $map
     * @param array $kb_array 查询哪个字段
     * @param array $kt_array 对应的查询类型
     * @param array $kv_array 对应的值
     */
    private static function condition2map(array &$map, array $kb_array, array $kt_array, array $kv_array)
    {
        if (!$kb_array || !$kt_array || !$kv_array) {
            return ;
        }
        foreach ($kb_array as $key => $value) {
            if (!static::$allowSearch || in_array($value, static::$allowSort)) {
                switch ($kt_array[$key]) {
                    case 'eq':
                        $map[] = [$value, '=', $kv_array[$key]];
                        break;
                    case 'neq':
                        $map[] = [$value, '<>', $kv_array[$key]];
                        break;
                    case 'gt':
                        $map[] = [$value, '>', $kv_array[$key]];
                        break;
                    case 'egt':
                        $map[] = [$value, '>=', $kv_array[$key]];
                        break;
                    case 'lt':
                        $map[] = [$value, '<', $kv_array[$key]];
                        break;
                    case 'elt':
                        $map[] = [$value, '<=', $kv_array[$key]];
                        break;
                    case 'like':
                        $map[] = [$value, 'like', '%' . $kv_array[$key] . '%'];
                        break;
                    case 'not_like':
                        $map[] = [$value, 'not like', '%' . $kv_array[$key] . '%'];
                        break;
                    case 'null':
                        $map[] = [$value, 'null'];
                        break;
                    case 'not_null':
                        $map[] = [$value, 'not null'];
                        break;
                    case 'in':
                        $map[] = [$value, 'in', str_replace('|', ',', $kv_array[$key])];
                        break;
                    case 'not_in':
                        $map[] = [$value, 'not in', str_replace('|', ',', $kv_array[$key])];
                        break;
                    case 'between':
                        $map[] = [$value, 'between', str_replace('|', ',', $kv_array[$key])];
                        break;
                    case 'not_between':
                        $map[] = [$value, 'not between', str_replace('|', ',', $kv_array[$key])];
                        break;
                }
            }
        }
    }
}
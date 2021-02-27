<?php

namespace app\member\model;

use app\base\model\BaseModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

class MemberAddress extends BaseModel
{
    /**
     * @param $id
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function getAddressList($id)
    {
        $map['id'] = $id;
        $map['status'] = 1;
        return self::getList([], [], $map);
    }

}
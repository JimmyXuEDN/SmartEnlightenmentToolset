<?php

namespace app\admin\model;

use app\base\model\BaseModel;

class AdminRole extends BaseModel
{

    public function accesses()
    {
        return $this->hasMany(AdminAccess::class, 'role_id', 'id');
    }

}
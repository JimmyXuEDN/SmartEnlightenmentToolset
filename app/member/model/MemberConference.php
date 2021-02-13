<?php

namespace app\member\model;

use app\base\model\BaseModel;
use app\conference\model\Conference;

class MemberConference extends BaseModel
{

    protected $pk = 'member_conference_id';

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'conference_id');
    }
}
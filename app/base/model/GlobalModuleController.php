<?php


namespace app\base\model;


class GlobalModuleController extends BaseModel
{

    protected $pk = 'controller_id';

    public function globalModule()
    {
        return $this->belongsTo(GlobalModule::class, 'module_id', 'id');
    }
}
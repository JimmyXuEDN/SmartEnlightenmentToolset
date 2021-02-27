<?php


namespace app\base\listener;

use app\base\model\GlobalConfigType;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

class SystemInit
{
    /**
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function handle()
    {
        GlobalConfigType::loadConfig();
    }
}
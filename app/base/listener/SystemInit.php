<?php


namespace app\base\listener;

use app\base\model\GlobalConfigType;

class SystemInit
{
    public function handle()
    {
        GlobalConfigType::loadConfig();
    }
}
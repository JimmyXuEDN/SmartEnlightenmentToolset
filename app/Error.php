<?php


namespace app;

class Error
{
    /**
     * 方法不存在，会运行
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return  saas_make_response(1003);
    }
}

<?php


namespace app\index\controller;


use app\base\exception\SaasException;

class Index
{
    /**
     * @return string
     */
    public function index()
    {
        return 'API engine is running.';
    }
}
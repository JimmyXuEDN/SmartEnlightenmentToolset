<?php


namespace app\index\controller;


class Index
{
    /**
     * @return string
     * @throws \app\base\exception\SaasException
     */
    public function index()
    {
        return 'API engine is running.';
    }
}
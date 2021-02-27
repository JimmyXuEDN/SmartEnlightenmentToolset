<?php

namespace app\base\exception;

use Exception;
use think\Response;

/**
 * 中断类型回复通过抛出错误返回
 * Class SaasException
 * @package app\base\exception
 * Auth Zhong
 */
class SaasException extends Exception
{

    /**
     * @var array 数据
     */
    private $data = [];

    /**
     * LogicException constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
        parent::__construct($data['responseHeader']['message'], $data['responseHeader']['returnCode']);
    }

    public function getData()
    {
        return $this->data;
    }
}
<?php

namespace pay\driver;

use \pay\exception\PayException;

class Base
{
    protected $config = [];


    protected function getConfig($key, $must = true)
    {
        if (!isset($this->config[$key])) {
            if ($must) {
                throw new PayException('need config:' . $key);
            } else {
                return false;
            }
        }

        return $this->config[$key];
    }


}
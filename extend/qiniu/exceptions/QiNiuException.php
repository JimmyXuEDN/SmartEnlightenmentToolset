<?php

namespace qiniu\exceptions;

use Exception;

class QiNiuException extends Exception
{
    /**
     * @param $message
     * @return QiNiuException
     */
    public static function withMessage($message)
    {
        return new static($message);
    }
}

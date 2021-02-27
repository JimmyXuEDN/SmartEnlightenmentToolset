<?php

/**
 * Created by PhpStorm.
 * User: LucasXu
 * Date: 2020/3/6
 * Time: 18:41
 */

namespace app\base\exception;

use think\exception\Handle;
use think\exception\HttpException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

class Http extends Handle
{
    public function render($request, Throwable $e): Response
    {
        // 参数验证错误
        if ($e instanceof ValidateException) {
            // return json($e->getError(), 422);
            return json(saas_make_response(ERROR_PARAMS_CHECK, ['validateMessage' => $e->getError()]));
        }

        // 请求异常
        if ($e instanceof HttpException && $request->isJson()) {
            return json(saas_make_response(ERROR_SYSTEM, ['message' => $e->getMessage(), 'code' => $e->getStatusCode()]));
        }

        // 请求异常
        if ($e instanceof HttpException && $request->isAjax()) {
            return response($e->getMessage(), $e->getStatusCode());
        }

        // 其他错误交给系统处理
        return parent::render($request, $e);
    }
}

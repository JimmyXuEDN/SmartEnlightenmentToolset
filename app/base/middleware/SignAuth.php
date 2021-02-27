<?php
declare (strict_types=1);

namespace app\base\middleware;

use app\base\exception\SaasException;
use app\base\model\GlobalModule;
use app\base\model\GlobalModuleController;
use app\Request;
use Closure;
use think\facade\Env;
use think\Response;

class SignAuth
{
    private $except = [
        '/'
    ];

    /**
     * 签名判断
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @throws SaasException
     */
    public function handle($request, Closure $next)
    {
        foreach ($this->except as $except) {
            if ($request->baseUrl() == $except) {
                return $next($request);
            }
        }

        if (!Env::get('app_debug')) {
            if (!$request->header('sign') || !$request->header('requesttimestamp') || !$this->checkRsa($request->header('sign'), $request->header('requesttimestamp'))) {
                return json(saas_make_response(3001));
            }
        }

        return $next($request);
    }

    /**
     * 验证签名
     * @param $sign
     * @param int $header_request_time
     * @return bool
     * @throws SaasException
     */
    private function checkRsa($sign, $header_request_time = 0)
    {
        // RSA解码参数
        $request_time_stamp = saas_rsa_decode($sign);
        // 能够正常解码且等于请求头中的请求时间requesttimestamp
        if (!is_null($request_time_stamp) && $request_time_stamp == $header_request_time) {
            return true;
        }
        return false;
    }
}

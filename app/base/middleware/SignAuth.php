<?php
declare (strict_types=1);

namespace app\base\middleware;

use app\base\model\GlobalModule;
use app\base\model\GlobalModuleController;
use think\facade\Env;
use think\Response;

class SignAuth
{
    private $except = [
        '/'
    ];

    /**
     * 签名判断
     * @param \app\Request $request
     * @param \Closure $next
     * @return Response
     * @throws \app\base\exception\SaasException
     */
    public function handle($request, \Closure $next)
    {
        foreach ($this->except as $except) {
            if ($request->baseUrl() == $except) {
                return $next($request);
            }
        }

        if (!Env::get('app_debug')) {
            if (!$request->header('sign') || !$request->header('requesttimestamp') || !$this->checkRsa($request->header('sign'), $request->header('requesttimestamp'))) {
                return json(saas_make_response(3001, [], 'error_sign'));
            }
        }

        return $next($request);
    }

    /**
     * 验证签名
     * @param $sign
     * @param int $header_request_time
     * @return bool
     * @throws \app\base\exception\SaasException
     */
    private function checkRsa($sign, $header_request_time = 0)
    {
        $private_key = saas_config('global.encrypt_private_key');
        $public_key = saas_config('global.encrypt_key');
        openssl_private_decrypt(base64_decode($sign), $decrypt_data, $private_key);
        if (strpos($decrypt_data, $public_key) !== false) {
            $request_time_stamp = explode('_', $decrypt_data);
            if (isset($request_time_stamp[1]) && $request_time_stamp[1] == $header_request_time) {
                return true;
            }
        }
        return false;
    }
}

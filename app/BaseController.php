<?php
declare (strict_types=1);

namespace app;

use think\App;
use think\exception\ValidateException;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
    }

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

    /**
     * 验证数据
     * @access protected
     * @param array $data 数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array $message 提示信息
     * @param bool $batch 是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

    /**
     * 返回数据给客户端
     * @param int $code //状态码
     * @param array $data //返回的数据
     * @param string $other_message //额外的信息
     * @param array $message_var
     * @return \think\response\Json
     */
    public function sendResponse(int $code, $data = [], $other_message = '', $message_var = [])
    {
        return json(saas_make_response($code, $data, $other_message, $message_var));
    }

    /**
     * 获取参数
     * @param string $key
     * @param bool $must
     * @param bool $default
     * @return mixed|array|string
     * @throws base\exception\SaasException
     */
    public function getParams($key = '', $must = false, $default = false)
    {
        if (empty($key)) {
            return $this->request->param();
        }
        if (strpos($key, '/')) {
            list($name, $type) = explode('/', $key);
        }
        $name = isset($name) ? $name : $key;
        if ($this->request->has($name)) {
            return $this->request->param($key);
        }
        if ($must) {
            saas_abort(ERROR_PARAMS, null, "system_error_lack_params", [$name]);
        }
        return $default;
    }

}

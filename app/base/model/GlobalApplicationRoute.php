<?php
/**
 * Created by PhpStorm.
 * User: LucasXu
 * Date: 2021/2/15
 * Time: 18:11
 * Class GlobalApplicationRoute
 * @package base
 */

namespace app\base\model;

use app\admin\model\AdminUser;
use app\member\model\Member;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Request;
use think\Model;
use think\model\relation\BelongsTo;

class GlobalApplicationRoute extends BaseModel
{
    public const REQUEST_METHOD = [
        'GET' => 1,
        'POST' => 2,
        'PUT' => 3,
        'DELETE' => 4,
        'PATCH' => 5
    ];
    /**
     * 属于一个应用
     * @return BelongsTo
     */
    public function application()
    {
        return $this->belongsTo('GlobalApplication');
    }

    /**
     * 根据请求参数检查路由是否存在于路由表
     * 存在返回路由，否则返回false
     * @param null $url
     * @param null $method
     * @return array|bool|Model
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function routeExistCheck ($url = null, $method = NULL)
    {
        if (is_null($url) || is_null($method)) {
            return false;
        }
        $record = self::where('url', $url)
            ->where('type_request', 'IN', [0, 6, self::REQUEST_METHOD[$method]])
            ->find();
        if (is_null($record)) {
            return false;
        }
        return $record;
    }

    /**
     * 根据路由Authorized要求检查用户登录状态
     * @param $record
     * @return bool
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function routeAuthorizedCheck($record)
    {
        if (intval($record->getAttr('authorized')) === 1) {
            switch (intval($record->getAttr('type_client'))) {
                case 1:
                    if (is_null(Member::checkLogin(Request::header('token', '')))) {
                        return false;
                    }
                    break;
                case 2:
                    if (is_null(AdminUser::checkLogin(Request::header('token', '')))) {
                        return false;
                    }
                    break;
                default:
                    return false;
            }
        }
        return true;
    }
}
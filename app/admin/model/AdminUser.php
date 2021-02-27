<?php

namespace app\admin\model;

use app\base\model\BaseModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;
use think\model\relation\BelongsTo;
use think\model\relation\HasOne;

class AdminUser extends BaseModel
{
    protected $hidden =[
        'password'
    ];

    /**
     * 属于一个角色
     * @return BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(AdminRole::class);
    }
    
    /**
     * 用户登录凭证token
     * @return HasOne
     */
    public function adminUserToken()
    {
        return $this->hasOne(AdminUserToken::class);
    }

    /**
     * @param string $account
     * @param string $password
     * @return array|Model|null|AdminUser
     */
    public static function login(string $account, string $password)
    {
        $map = [
            'account' => $account,
            'password' => $password,
        ];
        return self::where($map)->find();
    }

    /**
     * @param $token
     * @return array|Model|AdminUser
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function checkLogin($token)
    {
        return self::hasWhere('adminUserToken', function ($query) use ($token) {
            $query->where('token', $token);
        })->find();
    }

    /**
     * @return string
     */
    public function updateToken()
    {
        $data['token'] = uniqid();

        if ($this->adminUserToken) {
            $this->adminUserToken->save($data);
        } else {
            $this->adminUserToken()->save($data);
        }
        return $data['token'];
    }
}
<?php

namespace app\admin\model;

use app\base\model\BaseModel;

class AdminUser extends BaseModel
{
    protected $hidden =[
        'password'
    ];

    public function adminUserToken()
    {
        return $this->hasOne(AdminUserToken::class, 'user_id', 'id');
    }

    /**
     * @param string $account
     * @param string $password
     * @return array|\think\Model|null|AdminUser
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
     * @return array|\think\Model|AdminUser
     */
    public static function checkLogin($token)
    {
        return self::hasWhere('adminUserToken', function ($query) use ($token) {
            $query->where('token', $token);
        })->find();
    }


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
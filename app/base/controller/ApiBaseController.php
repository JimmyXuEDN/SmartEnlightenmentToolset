<?php


namespace app\base\controller;


use app\base\exception\SaasException;
use app\BaseController;
use app\member\model\Member;

class ApiBaseController extends BaseController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        $this->checkLogin();
    }

    /**
     * 设置用户信息
     */
    public function checkLogin()
    {
        $this->request->setMember(Member::checkLogin($this->request->header('token', '')));
    }

    /**
     * 获取用户信息
     * @return Member|null
     */
    public function getMember()
    {
        return $this->request->getMember();
    }

    /**
     * 获取用户主键
     * @return mixed
     * @throws SaasException
     */
    public function getMemberId()
    {
        if (is_null($this->getMember())) {
            saas_abort(ERROR_TOKEN);
        }
        return $this->getMember()->id;
    }
}
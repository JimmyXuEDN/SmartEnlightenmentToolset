<?php


namespace app\base\controller;


use app\BaseController;
use app\member\model\Member;

class ApiBaseController extends BaseController
{

    public function initialize()
    {
        $this->checkLogin();
    }


    public function checkLogin()
    {
        $this->request->setMember(Member::checkLogin($this->request->header('token', '')));
        if (!$this->request->getMember() && $this->request->isModuleControllerNeedLogin()) {
            saas_abort(ERROR_TOKEN);
        }
    }

    public function getMember()
    {
        return $this->request->getMember();
    }

    public function getMemberId()
    {
        if (is_null($this->getMember())) {
            saas_abort(ERROR_TOKEN);
        }
        return $this->getMember()->member_id;
    }
}
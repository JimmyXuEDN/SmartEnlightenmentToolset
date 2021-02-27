<?php

namespace app\member\controller;

use app\base\controller\ApiBaseController;
use app\base\exception\SaasException;
use app\base\util\SmsUtil;
use app\auth\model\AuthMember;
use app\member\model\Member;
use app\message\model\Message;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Request;
use think\response\Json;

class ApiMember extends ApiBaseController
{
    /**
     * 获取用户信息
     * @return Json
     */
    public function detail()
    {
        return $this->sendResponse(SUCCESS, $this->getMember());
    }

    /**
     * 更新用户基本信息
     * @return Json
     * @throws SaasException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function update()
    {
        $model = Member::find($this->getMemberId());
        // 字段过滤
        $data = $this->request->only(['mobile', 'nick_name', 'avatar', 'gender', 'birthday']);
        $model->save($data);
        return $this->sendResponse(SUCCESS, $model);
    }
}
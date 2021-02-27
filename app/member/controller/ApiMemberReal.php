<?php

namespace app\member\controller;

use app\base\controller\ApiBaseController;
use app\base\exception\SaasException;
use app\member\model\MemberReal;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Response;

class ApiMemberReal extends ApiBaseController
{
    /**
     * 添加
     * @return Response
     * @throws SaasException
     */
    public function save()
    {
        $member = $this->getMember();
        $data = $this->getParams();
        if ($member->memberReal) {
            $data['is_verify'] = 0;
            $data['verify_message'] = '';
            $member->memberReal->save($data);
        } else {
            $member->memberReal()->save($data);
        }
        return $this->sendResponse(SUCCESS);
    }

    /**
     * 详情
     * @param int $id
     * @return Response
     * @throws SaasException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function read($id)
    {
        $res = MemberReal::where(['id' => $this->getMemberId()])->find();
        return $this->sendResponse(0, $res);
    }
}
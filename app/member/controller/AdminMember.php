<?php

namespace app\member\controller;

use app\base\controller\AdminBaseController;
use app\auth\model\AuthMember;
use app\base\exception\SaasException;
use app\member\model\Member;
use app\member\model\MemberAddress;
use app\member\model\MemberFeedback;
use app\member\model\MemberToken;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Response;
use think\response\Json;

class AdminMember extends AdminBaseController
{
    /**
     * 显示资源列表
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function index()
    {
        $res = Member::getList(['authMember']);
        return $this->sendResponse(0, $res);
    }

    /**
     * 保存新建的资源
     * @return Response
     * @throws SaasException
     */
    public function save()
    {
        $data = $this->getParams();
        $data['type'] = in_array(intval($data['identity_type']), [1, 6]);
        if (in_array(intval($data['identity_type']), [1, 6])) {
            $this->validate($data, 'app\auth\validate\AuthMemberValidate.AddAuth');
        } else {
            $this->validate($data, 'app\auth\validate\AuthMemberValidate.LoginWxOfficial');
        }
        $auth = new AuthMember();
        $auth->genMember($data);

        return $this->sendResponse(SUCCESS, $data);
    }

    /**
     * 显示指定的资源
     * @param int $id
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function read($id)
    {
        $res = Member::with(['authMember'])->find($id);
        return $this->sendResponse(0, $res);
    }

    /**
     * 保存更新的资源
     *
     * @param int $id
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws SaasException
     */
    public function update($id)
    {
        $model = Member::find($id);
        $model->save($this->getParams());
        return $this->sendResponse(0);
    }

    /**
     * 删除指定资源
     * @param int $id
     */
    public function delete($id)
    {
        Member::destroy($id);
        MemberToken::where('member_id', $id)->delete();
        AuthMember::where('member_id', $id)->delete();
        MemberFeedback::where('member_id', $id)->delete();
        MemberAddress::where('member_id', $id)->delete();
        return $this->sendResponse(SUCCESS);
    }
}
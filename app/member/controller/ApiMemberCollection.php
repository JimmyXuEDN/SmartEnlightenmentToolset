<?php

namespace app\member\controller;

use app\base\controller\ApiBaseController;
use app\member\model\MemberCollection;

class ApiMemberCollection extends ApiBaseController
{

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $res = MemberCollection::getCollectionList($this->getMemberId());
        return $this->sendResponse(SUCCESS, $res);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //

    }

    /**
     * 保存新建的资源
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save()
    {
        $collection = new MemberCollection();
        $collection->member_id = $this->getMemberId();
        $collection->type = $this->getParams('type');
        $collection->aim_id = $this->getParams('aim_id');
        $collection->save();
        return $this->sendResponse(SUCCESS);
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        $res = model('MemberAddress')->getAddressDetail($id);
        $this->sendResponse(SUCCESS, $res);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {

    }

    /**
     * 保存更新的资源
     *
     * @param \think\Request $request
     * @param int $id
     * @return \think\Response
     */
    public function update($id)
    {
        //添加地址
        $map['addr_id'] = $id;
        $data = $this->request->post();
        $data['member_id'] = $this->member['member_id'];
        if (isset($data['is_default']) && $data['is_default'] == 1) {
            $def_map['member_id'] = $this->member['member_id'];
            $update['is_default'] = 0;
            model('MemberAddress')->where($def_map)->update($update);
        }
        $res = model('MemberAddress')->validate(true)->where($map)->update($data);

        if ($res === false) {
            $this->sendResponse(ERROR_PARAMS, array(), model('MemberAddress')->getError());
        } else {
            $this->sendResponse(0);
        }
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $collect_id = $id;
        $res = MemberCollection::deleteCollect($collect_id);
        if ($res !== false) {
            return $this->sendResponse(SUCCESS);

        } else {
            return $this->sendResponse(ERROR_SQL);
        }
    }
}
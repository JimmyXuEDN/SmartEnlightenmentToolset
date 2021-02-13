<?php

namespace app\member\controller;

use app\base\controller\ApiBaseController;
use app\member\model\MemberCart;

class ApiMemberCart extends ApiBaseController
{

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $res = MemberCart::getCartList($this->getMemberId());
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
        //添加购物车
        $goods = $this->getParams('goods');
        $cart_array = array();
        foreach ($goods as $k => $v) {
            $cart_map['sku_id'] = $v['sku_id'];
            $cart_map['member_id'] = $this->getMemberId();
            $cart = MemberCart::where($cart_map)->find();
            if (!is_null($cart)) {
                $cart->num = $cart->num + $v['num'];
                $cart->save();
            } else {
                $v['member_id'] = $this->getMemberId();
                MemberCart::create($v);
            }

        }
        return $this->sendResponse(0);
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {

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
        $data['sku_id'] = $this->getParams('sku_id');
        $data['num'] = $this->getParams('num');
        $res = MemberCart::where('id', $id)->update($data);
        if ($res !== false) {
            return $this->sendResponse(SUCCESS);
        } else {
            return $this->sendResponse(ERROR_SQL);
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
        $res = MemberCart::destroy($id);
        if ($res !== false) {
            return $this->sendResponse(SUCCESS);

        } else {
            return $this->sendResponse(ERROR_SQL);
        }
    }
}
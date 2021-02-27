<?php
/**
 * Created by PhpStorm.
 * User: mzy
 * Date: 2020/3/22
 * Time: 14:02
 */

namespace app\admin\controller;

use app\admin\model\AdminAccess;
use app\admin\model\AdminRole;
use app\admin\validate\AdminMenuValidate;
use app\base\controller\AdminBaseController;
use app\admin\model\AdminMenu;
use app\base\exception\SaasException;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\response\Json;

/**
 * Class Menu
 * @package admin
 */
class Menu extends AdminBaseController
{
    /**
     * 显示资源列表
     * @return Json
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws \think\db\exception\DbException
     */
    public function index()
    {
        $res = AdminMenu::getList(['route']);
        return $this->sendResponse(SUCCESS, $res);
    }

    /**
     * 保存新建的资源
     * @return Json
     * @throws SaasException
     */
    public function save()
    {
        $this->validate($this->getParams(), AdminMenuValidate::class);
        AdminMenu::create($this->getParams());
        return $this->sendResponse(SUCCESS);
    }

    /**
     * 显示指定的资源
     * @param int $id
     * @return Json
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws \think\db\exception\DbException
     */
    public function read($id)
    {
        $res = AdminMenu::find($id, ['route']);
        return $this->sendResponse(SUCCESS, $res);
    }

    /**
     * 保存更新的资源
     * @param int $id
     * @return Json
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws SaasException
     * @throws \think\db\exception\DbException
     */
    public function update($id)
    {
        $this->validate($this->getParams(), AdminMenuValidate::class);
        $model = AdminMenu::find($id);
        $model->save($this->getParams());
        return $this->sendResponse(SUCCESS);
    }

    /**
     * 删除指定资源
     * @param int $id
     * @return Json
     */
    public function delete($id)
    {
        AdminMenu::destroy($id);
        return $this->sendResponse(SUCCESS);
    }
}
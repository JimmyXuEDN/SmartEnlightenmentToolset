<?php
namespace app;

// 应用请求对象类
use app\admin\model\AdminUser;
use app\base\model\GlobalModule;
use app\base\model\GlobalModuleController;
use app\member\model\Member;

class Request extends \think\Request
{
    /**
     * 变量全局过滤
     * @var string[]
     */
    protected $filter = ["trim", "htmlspecialchars", "addslashes", "strip_tags"];

    /**
     * @var GlobalModule
     */
    private $module = null;

    /**
     * @var GlobalModuleController
     */
    private $moduleController = null;

    /**
     * @var Member
     */
    private $member = null;


    /**
     * @var AdminUser
     */
    private $admin = null;


    /**
     * @return GlobalModule|null
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param GlobalModule|null $module
     */
    public function setModule($module): void
    {
        $this->module = $module;
    }

    /**
     * 模块是否安装
     * @return bool
     */
    public function isModuleInstall(): bool
    {
        if (is_null($this->getModule()) || is_null($this->getModuleController())) {
            return false;
        }
        return $this->getModule()->is_installed === 1 ? true : false;
    }

    /**
     * 模块是否可用
     * @return bool
     */
    public function isModuleAvailable(): bool
    {
        if (is_null($this->getModule()) || is_null($this->getModuleController())) {
            return false;
        }
        return $this->getModule()->is_available === 1 ? true : false;
    }

    /**
     * @return GlobalModuleController|null
     */
    public function getModuleController()
    {
        return $this->moduleController;
    }

    /**
     * @param GlobalModuleController|null $moduleController
     */
    public function setModuleController($moduleController): void
    {
        $this->moduleController = $moduleController;
    }

    /**
     * 是否强制登录
     * @return bool
     */
    public function isModuleControllerNeedLogin(): bool
    {
        if (is_null($this->getModuleController())) {
            return false;
        }
        return $this->getModuleController()->need_login === 1 ? true : false;
    }

    /**
     * @return Member|null
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @param Member|null $member
     */
    public function setMember($member): void
    {
        $this->member = $member;
    }

    /**
     * @return AdminUser
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param AdminUser $admin
     */
    public function setAdmin($admin): void
    {
        $this->admin = $admin;
    }
}

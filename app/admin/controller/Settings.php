<?php
/**
 * Settings 系统设置控制器
 */

namespace app\admin\controller;

class Settings extends AdminBase
{


    /**
     * 系统设置
     */
    public function index()
    {
        IS_POST && $this->jump($this->logicSettings->editSettings($this->param));

        $this->assign('userList',$this->logicUsers->getUserList(['IsActive'=>1],'Id,Name'));

        $this->assign('info',$this->logicSettings->index());

        return $this->fetch();
    }

    /**
     * 在冰箱后台注册接种点
     */
    public function setSettingsInfo()
    {
        return $this->logicSettings->setSettingsInfo($this->param);
    }


}
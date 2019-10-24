<?php
/*
 * @Descripttion: Positionfridge 控制器
 * @Author: fqm
 * @Date: 2019-08-21 14:42:35
 */

namespace app\admin\controller;

class Positionfridge extends AdminBase
{

    /**
     * 冰箱注册列表
     */
    public function position_fridge_list()
    {
        $where = $this->logicPositionfridge->getWhere($this->param);

        $this->assign('list',$this->logicPositionfridge->position_fridge_list($where));

        return $this->fetch('list');
    }

    /**
     * 冰箱详细信息
     */
    public function detail()
    {
        $this->assign('info',$this->logicPositionfridge->detail($this->param));

        return $this->fetch('detail');
    }




}

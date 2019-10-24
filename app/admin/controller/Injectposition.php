<?php
/*
 * @Descripttion: Injectposition控制器
 * @Author: fqm
 * @Date: 2019-08-20 08:12:59
 */

namespace app\admin\controller;

class Injectposition extends AdminBase
{

    /**
     * 接种地点列表
     */
    public function injectPositionList()
    {
        
        return $this->fetch('list');
    }

    /**
     * 获取接种点列表
     */
    public function getInjectPositionList()
    {

        return $this->logicInjectposition->injectPositionList($this->param);
    }

    /**
     * 接种地点是否通过
     */
    public function set_status()
    {
        $id = input('id');
        $status = input('status');

        $reject = input('reject');
        return $this->logicInjectposition->set_status($id, $status, $reject);
    }

    /**
     * 查看接种点信息
     */
    public function getInjectPosistionDetail()
    {
        $this->assign('info',$this->logicInjectposition->getInjectPosistionDetail($this->param));
        return $this->fetch('detail');
    }
    

}

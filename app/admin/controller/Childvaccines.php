<?php
/**
 * childvaccines 孩子接种疫苗历史
 */

namespace app\admin\controller;

class Childvaccines extends AdminBase
{



    /**
     * 获取当前孩子接种历史
     * ChildId
     */
    public function getChildvaccines()
    {
        return $this->logicChildvaccines->getChildvaccines($this->param);
    }
}
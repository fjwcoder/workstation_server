<?php
/*
 * @Descripttion: Positionoperator 控制器
 * @Author: fqm
 * @Date: 2019-08-22 15:45:08
 */

namespace app\api\controller;

class Positionoperator extends ApiBase
{

    /**
     * 添加操作人员
     */
    public function addOperator()
    {
        return $this->apiReturn($this->logicPositionoperator->addOperator($this->param));
    }

    /**
     * 本接种点操作人员列表
     */
    public function operatorList()
    {
        return $this->apiReturn($this->logicPositionoperator->operatorList($this->param));
    }

    /**
     * 编辑操作人员
     */
    public function editOperator()
    {
        return $this->apiReturn($this->logicPositionoperator->editOperator($this->param));
    }

    /**
     * 删除操作人员
     */
    public function delOperator()
    {
        return $this->apiReturn($this->logicPositionoperator->delOperator($this->param));
    }

    /**
     * 获取操作人员详情
     * add by fqm in 19.9.6
     */
    public function getOperator()
    {
        return $this->apiReturn($this->logicPositionoperator->getOperator($this->param));
    }


}

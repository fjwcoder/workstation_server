<?php
/**
 * Vaccinations 接种流水API控制器
 */

namespace app\api\controller;

class Vaccinations extends ApiBase
{

    /**
     * 获取待接种队列
     */
    public function getWaitingInjectList()
    {
        return $this->apiReturn($this->logicVaccinations->getWaitingInjectList());
    }

    /**
     * 完成接种
     */
    public function completeInject()
    {
        return $this->apiReturn($this->logicVaccinations->completeInject($this->param));
    }
}
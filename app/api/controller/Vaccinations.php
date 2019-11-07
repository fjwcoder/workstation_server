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
     * 查看待接种详情
     */
    public function getWaitingInjectInfo()
    {
        return $this->apiReturn($this->logicVaccinations->getWaitingInjectInfo($this->param));
    }

    /**
     * 完成接种
     */
    public function completeInject()
    {
        return $this->apiReturn($this->logicVaccinations->completeInject($this->param));
    }

    /**
     * 下一位
     */
    public function nextNumber()
    {
        return $this->apiReturn($this->logicVaccinations->nextNumber($this->param));
    }
}
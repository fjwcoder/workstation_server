<?php
/*
 * @Descripttion: Injectposition 接种点api控制器
 * @Author: fqm
 * @Date: 2019-08-20 14:10:11
 */

namespace app\api\controller;

class Injectposition extends ApiBase
{


    /**
     * 获取接种点列表
     */
    public function positionList()
    {
        return $this->apiReturn($this->logicInjectposition->positionList($this->param));
    }

    /**
     * 查看接种点详情
     */
    public function positionInfo()
    {
        return $this->apiReturn($this->logicInjectposition->positionInfo($this->param));
    }




}

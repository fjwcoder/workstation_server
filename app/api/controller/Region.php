<?php
/*
 * @Descripttion: Region 控制器
 * @Author: fqm
 * @Date: 2019-09-05 09:35:50
 */

namespace app\api\controller;

class Region extends ApiBase
{

    /**
     * 获取城市接口
     */
    public function getCity()
    {
        return $this->apiReturn($this->logicRegion->getCity($this->param));
    }
    


    
}
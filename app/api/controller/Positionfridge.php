<?php
/*
 * @Descripttion: Positionfridge 控制器
 * @Author: fqm
 * @Date: 2019-08-21 11:41:11
 */

namespace app\api\controller;

class Positionfridge extends ApiBase
{

    /**
     * 冰箱注册
     * 冰箱注册完成后 返回RSA加密的unique_code
     */
    public function regist()
    {
        return $this->apiReturn($this->logicPositionfridge->regist($this->param));

    }




}

<?php
/**
 * 叫号信息推送后保存
 */

namespace app\api\controller;

class Queue extends ApiBase
{

    /**
     * 取号时，添加数据
     */
    public function push()
    {

        return $this->logicQueue->push(file_get_contents("php://input"));
        // return $this->logicQueue->push($this->param);
    }



}
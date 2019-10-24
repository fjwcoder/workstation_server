<?php
/**
 * 叫号信息推送后保存
 */

namespace app\api\controller;

class Queue extends ApiBase
{


    public function push()
    {

        return $this->logicQueue->push(file_get_contents("php://input"));
    }



}
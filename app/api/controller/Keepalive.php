<?php
/*
 * @Descripttion: Keepalive 控制器
 * @Author: fqm
 * @Date: 2019-09-02 09:36:58
 */

namespace app\api\controller;

class Keepalive extends ApiBase
{

    /**
     * 冰箱保活接口,规定心跳,向服务器发送 当前冰箱unique_code,时间戳,温度
     */
    // fridgeHeartBeat
    public function fridgeHeartBeat()
    {

        return $this->apiReturn($this->logicKeepalive->fridgeHeartBeat($this->param));
    }



}

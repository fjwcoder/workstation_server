<?php
/**
 * Userloginattempts 用户访问日志逻辑层
 */

namespace app\admin\logic;

class Userloginattempts extends AdminBase
{



    /**
     * 添加用户登录日志
     */
    public function addSingLog($data){

        $data['TenantId'] = 1; // 租户ID

        $data['TenancyName'] = 'Default'; // 租户名称

        $data['CreationTime'] = date("Y-m-d H:i:s"); // 创建时间

        $data['ClientIpAddress'] = USER_IP; // 用户ip

        $data['BrowserInfo'] = $_SERVER['HTTP_USER_AGENT']; // 浏览器头部信息

        // dump($data);die;

        $this->modelUserloginattempts->setInfo($data);

    }


    /**
     * 获取日志列表
     */
    public function getLogList($where = [], $field = true, $order = '', $paginate = false)
    {

        $logList = $this->modelUserloginattempts->getList($where, $field, $order, $paginate);

        return $logList;

    }



}
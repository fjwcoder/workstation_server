<?php
/*
 * @Descripttion: Injectposition逻辑层
 * @Author: fqm
 * @Date: 2019-08-20 08:19:18
 */

namespace app\admin\logic;

class Injectposition extends AdminBase
{

    /**
     * 获取接种地点列表
     */
    public function injectPositionList($data)
    {
        $where = [];

        $field = true;

        !empty($data['status']) && $where['status'] = $data['status'];
        
        !empty($data['search']) && $where['account|name|phone|name|province|city|district'] = ['like', '%'.$data['search'].'%'];

        return $this->modelMInjectPosition->getList($where, $field, 'id desc', 10);

    }

    /**
     * 设置接种点状态
     */
    public function set_status($id, $status, $reject = '')
    {
        $data = [
            'status'=>$status,
            'reject'=>$reject,
        ];
        $where = ['id'=>$id];
        $res =  $this->modelMInjectPosition->setInfo($data, $where);
        return $res?['code'=>200, 'msg'=>'操作成功']:['code'=>400, 'msg'=>'操作失败'];
    }

    /**
     * 查看接种点信息
     */
    public function getInjectPosistionDetail($param)
    {
        // dump($param);
        $where = ['id'=>$param['id']];

        return $this->modelMInjectPosition->getInfo($where);
    }



}
<?php
/*
 * @Descripttion: Region 逻辑层
 * @Author: fqm
 * @Date: 2019-09-05 09:39:48
 */

namespace app\api\logic;

class Region extends ApiBase
{

    /**
     * 获取城市接口
     */
    public function getCity($param = [])
    {

        $where = [
            'pid' => !empty($param['pid']) ? $param['pid'] : 0,
            'status' => array('in','1,2'),
        ];

        $field = 'id, name';

        $data = $this->modelRegion->getList($where, $field, 'id asc', false);

        return $data;
    }
}

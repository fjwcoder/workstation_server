<?php
/*
 * @Descripttion: Region 逻辑层
 * @Author: fqm
 * @Date: 2019-08-21 15:36:10
 */

namespace app\common\logic;
use think\Db;

class Region extends LogicBase
{

    /**
     * 获取城市列表
     */
    public function get_city($pid = 0,$field = 'id,name')
    {
        $where = [
            'pid' => $pid,
        ];

        $data = Db::name('region')->where($where)->field($field)->select();
        return $data;
    }

}



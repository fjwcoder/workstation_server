<?php
/*
 * @Descripttion: Positionfridge 逻辑层
 * @Author: fqm
 * @Date: 2019-08-21 14:47:28
 */

namespace app\admin\logic;

class Positionfridge extends AdminBase
{

    /**
     * 冰箱注册列表
     */
    public function position_fridge_list($where = [])
    {

        $this->modelMPositionFridge->alias('a');
        
        $join = [
                [SYS_DB_PREFIX . 'm_position_operator o', 'a.id = o.fridge_id'],
            ];
        
        $this->modelMPositionFridge->join = $join;

        $field = 'a.id, a.position_id, a.unique_code, a.mac_address, a.create_time, o.user_type, o.user_type, o.name, o.mobile, o.id_card';

        $order = 'a.id desc';

        return $this->modelMPositionFridge->getList($where, $field, $order, 10);
    }

    /**
     * 冰箱详细信息
     */
    public function detail($param = [])
    {
        $where = [
            'a.id' => $param['id']
        ];
        
        $this->modelMPositionFridge->alias('a');
        
        $join = [
                [SYS_DB_PREFIX . 'm_position_operator o', 'a.id = o.fridge_id'],
            ];
        
        $this->modelMPositionFridge->join = $join;

        $field = 'a.id, a.position_id, a.unique_code, a.mac_address, a.create_time, o.user_type, o.user_type, o.name, o.mobile, o.id_card';

        return $this->modelMPositionFridge->getInfo($where, $field);
    }

    /**
     * 获取冰箱列表搜索条件
     */
    public function getWhere($param = [])
    {
        $where = [];
        
        !empty($param['search_data']) && $where['a.position_id|a.unique_code|a.mac_address|o.name|o.mobile|o.id_card'] = ['like', '%'.$param['search_data'].'%'];
        
        return $where;
    }



}

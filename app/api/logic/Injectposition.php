<?php
/*
 * @Descripttion: Injectposition 接种点api逻辑层
 * @Author: fqm
 * @Date: 2019-08-20 14:12:19
 */

namespace app\api\logic;

class Injectposition extends ApiBase
{

    /**
     * 获取接种点列表
     */
    public function positionList($param = [])
    {

        $where = [
            'status' => 1,
        ];

        // edit fqm 19.8.21 修改省/市/区 id 的查询条件
        // !empty($param['province']) ? $where['province'] = $param['province'] : '';
        // !empty($param['city']) ? $where['city'] = $param['city'] : '';
        // !empty($param['district']) ? $where['district'] = $param['district'] : '';
        // edit by fqm in 19.9.5 修改查询条件
        !empty($param['town']) ? $where['town'] = $param['town'] : '';
        if(empty($where['town'])){
            return [API_CODE_NAME => 40007,  API_MSG_NAME =>'请选择街道'];
        }

        // by fqm in 19.9.2 添加返回字段 
        $field = 'id, province, city, district, town, address, phone, name, business, permit, province_city_district';

        $oeder = 'id desc';

        // edit by fqm in 19.9.5 不传list_rows不分页
        !empty($param['list_rows']) ? $paginate = $param['list_rows'] : $paginate = false;

        return $this->modelMInjectPosition->getList($where, $field, $oeder, $paginate);

    }

    /**
     * 查看接种点详情
     */
    public function positionInfo($param = [])
    {
        $where = ['id'=>$param['id']];

        // by fqm in 19.9.2 添加返回字段 
        $field = 'id, province, city, district, town, address, phone, name, business, permit, province_city_district';

        return $this->modelMInjectPosition->getInfo($where, $field);
    }




}
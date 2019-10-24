<?php
/**
 * Vaccinations 接种流水控制器
 */

namespace app\index\controller;

class Vaccinations extends IndexBase
{


    /**
     * 待登记队列
     * 已经取了号，还没有登记的
     */
    public function registerList()
    {

        return $this->fetch();
    }
    /**
     * 获取待登记队列列表
     */
    public function getRegisterList()
    {
        $where = [];

        $search_data = input('search_data');

        $where = [];

        if(!empty($search_data) && $search_data !== ''){
            // $where['search_data'] = ['like', '%'.$search_data.'%'],
        }

        

        $list = $this->logicVaccinations->getRegisterList($where);

        return $list;

    }



}
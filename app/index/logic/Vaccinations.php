<?php
/**
 * Vaccinations 接种流水逻辑层 
 * by fqm in 19.9.30
 */

namespace app\index\logic;

class Vaccinations extends IndexBase
{


    /**
     * 获取待登记队列列表
     * 已经取了号，还没有登记的
     */
    public function getRegisterList($where = [])
    {
        $today = date('Y-m-d');
        // $today = '2019-09-03';

        $where['CreationTime'] = ['like', '%'.$today.'%'];
        $where['State'] = 0;

        $list = $this->modelVaccinations->getList($where, 'Id, CreationTime, Number, ChildId', 'CreationTime asc', false);

        return $list;
    }

    /**
     * 待接种队列
     * 已经登记，等待接种的
     */
    public function injectList($where = [])
    {
        $today = date('Y-m-d');
        // $today = '2019-09-03';

        $where['CreationTime'] = ['like', '%'.$today.'%'];
        $where['State'] = 1;

        $list = $this->modelVaccinations->getList($where, 'Id, CreationTime, Number, ChildId', 'CreationTime asc', false);

        return $list;
    }

    /**
     * 留观队列
     * 已经接种，需要观察的
     */
    public function observationList($where = [])
    {
        $today = date('Y-m-d');
        // $today = '2019-09-03';

        $where['CreationTime'] = ['like', '%'.$today.'%'];
        $where['State'] = 3;

        $list = $this->modelVaccinations->getList($where, 'Id, CreationTime, Number, ChildId', 'CreationTime asc', false);

        return $list;
    }

    /**
     * 完成队列
     */



}
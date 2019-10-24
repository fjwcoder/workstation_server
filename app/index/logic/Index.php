<?php
/***
 * index 逻辑层
 * by fqm in 19.9.30
 */

namespace app\index\logic;

class Index extends IndexBase
{




    /**
     * index方法 获取接种数量等
     */
    public function index()
    {
        $today = date('Y-m-d');
        // $today = '2019-09-03';

        $where = [
            'CreationTime'=>['like', '%'.$today.'%'],
        ];
        // 今日取号
        // $where['State'] = 0;
        $data['takeTheNumber'] = $this->modelVaccinations->stat($where,'count','Id');
        // 今日登记
        $where['State'] = 1;
        $data['registerNumber'] = $this->modelVaccinations->stat($where,'count','Id');
        // 今日接种
        $where['State'] = ['>=',2];
        $data['injectNumber'] = $this->modelVaccinations->stat($where,'count','Id');
        // 今日留观
        $where['State'] = 3;
        $data['observationNumber'] = $this->modelVaccinations->stat($where,'count','Id');
        // 今日完成
        $where['State'] = 4;
        $data['completeNumber'] = $this->modelVaccinations->stat($where,'count','Id');

        return $data;


    }





}